<?php

  /**
   * Basic authentication provider
   * 
   * Although basic authentication provider is part of the framework it makes 
   * some assumptions about how authentication should work:
   * 
   * - Users are data objects that use email and password pairs for 
   *   authentication
   * - There is per user session ID that is used to track sessions
   * - We keep session ID in $_SESSION and $_COOKIE
   * - We can remember session in a cookie for 14 days
   * 
   * @package angie.library.authentication
   * @subpackage provider
   */
  class BasicAuthenticationProvider extends AuthenticationProvider {
    
    /**
     * Session ID variable name (in $_SESSION and $_COOKIE)
     *
     * @var string
     */
    var $session_id_var_name = 'sid';
    
    /**
     * ID of current session, set by logUserIn()
     *
     * @var integer
     */
    var $session_id = null;
    
    /**
     * Construct authentication provider
     *
     * @param void
     * @return BasicAuthenticationProvider
     */
    function __construct() {
      parent::__construct();
      
      if(defined('APPLICATION_NAME')) {
        $this->session_id_var_name = APPLICATION_NAME . '_sid';
      } // if
    } // __construct
    
    /**
     * Initialize basic authentication
     * 
     * Try to get user from cookie or session
     *
     * @param void
     * @return User
     */
    function initialize() {
      db_execute('DELETE FROM ' . TABLE_PREFIX . 'user_sessions WHERE expires_on < ?', date(DATETIME_MYSQL)); // Expire old sessions
      
      $cookie_session_id = cookie_get($this->session_id_var_name);
      
      $settings = array(
        'remember' => false,
        'new_visit' => false,
      );
      
      if($cookie_session_id && strpos($cookie_session_id, '/') !== false) {
        list($session_id, $session_key, $session_time) = explode('/', $cookie_session_id);
        
        if((time() - USER_SESSION_LIFETIME) > strtotime($session_time)) {
          $settings['new_visit'] = true;
        } // if
        
        $user = Users::findBySessionId($session_id, $session_key);
        
        if(instance_of($user, 'User')) {
          return $this->logUserIn($user, $settings, $session_id);
        } // if
      } // if
      
      return null;
    } // init
    
    /**
     * Try to log user in with given credentials
     *
     * @param array $credentials
     * @return User
     */
    function authenticate($credentials) {
      $email    = array_var($credentials, 'email');
      $password = array_var($credentials, 'password');
      $remember = (boolean) array_var($credentials, 'remember', false);
      
      $user = Users::findByEmail($email);
      
      if(!instance_of($user, 'User')) {
        return new Error('User is not registered');
      } // if
      
      if(!$user->isCurrentPassword($password)) {
        return new Error('Invalid password');
      } // if
      
      return $this->logUserIn($user, array(
        'remember' => $remember,
        'new_visit' => true,
      ));
    } // authenticate
    
    /**
     * Log user in
     * 
     * This function will recognize following settings:
     * 
     * - remeber - remember session ID in cookie for 14 days
     * - new_visit - mark this as new visit (set last visit on timestamp to 
     *   current time)
     * - silent - used for a quick and dirty authentication, usually for feeds
     * 
     * $session_id is ID of existing session
     *
     * @param User $user
     * @param array $settings
     * @param integer $existing_session_id
     * @return User
     */
    function logUserIn($user, $settings = null, $existing_session_id = null) {
      if(isset($settings['silent']) && $settings['silent']) {
        return parent::logUserIn($user);
      } else {
        
        db_begin_work();
        
        $users_table = TABLE_PREFIX . 'users';
        $user_sessions_table = TABLE_PREFIX . 'user_sessions';
        
        $remember = (boolean) array_var($settings, 'remember', false);
        $new_visit = (boolean) array_var($settings, 'new_visit', false);
        
        // Some initial data
        $session_id = null;
        $new_expires_on = $remember ? time() + 1209600 : time() + USER_SESSION_LIFETIME; // 30 minutes or 2 weeks?
        
        // Existing session
        if($existing_session_id) {
          $existing_session_data = db_execute_one("SELECT remember, session_key FROM $user_sessions_table WHERE id = ?", $existing_session_id);
          
          if($existing_session_data && isset($existing_session_data['remember']) && isset($existing_session_data['session_key'])) {
            if($existing_session_data['remember']) {
              $new_expires_on = time() + 1209600;
            } // if
            
            $session_key = $existing_session_data['session_key'];
            
            $update = db_execute("UPDATE $user_sessions_table SET user_ip = ?, user_agent = ?, last_activity_on = NOW(), expires_on = ?, visits = visits + 1 WHERE id = ?", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], date(DATETIME_MYSQL, $new_expires_on), $existing_session_id);
            if($update && !is_error($update)) {
              $session_id = $existing_session_id;
            } else {
              db_rollback();
              return $update;
            } // if
          } // if
        } // if
        
        // New session?
        if($session_id === null) {
          do {
            $session_key = make_string(40);
          } while(array_var(db_execute_one("SELECT COUNT(id) AS 'row_count' FROM $user_sessions_table WHERE session_key = ?", $session_key), 'row_count') > 0);
          
          $insert = db_execute("INSERT INTO $user_sessions_table (user_id, user_ip, user_agent, visits, remember, created_on, last_activity_on, expires_on, session_key) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)", $user->getId(), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], 1, (integer) $remember, date(DATETIME_MYSQL), date(DATETIME_MYSQL, $new_expires_on), $session_key);
          if($insert && !is_error($insert)) {
            $session_id = db_last_insert_id();
          } else {
            db_rollback();
            return $insert;
          } // if
        } // if
        
        // Update last visit time
        if($new_visit) {
          $update = db_execute("UPDATE $users_table SET last_visit_on = last_login_on, last_login_on = ?, last_activity_on = ? WHERE id = ?", date(DATETIME_MYSQL), date(DATETIME_MYSQL), $user->getId());
        } else {
          $update = db_execute("UPDATE $users_table SET last_activity_on = ? WHERE id = ?", date(DATETIME_MYSQL), $user->getId());
        } // if
        
        if($update && !is_error($update)) {
          db_commit();
        } else {
          db_rollback();
          
          return $update;
        } // if
        
        $this->session_id = $session_id; // remember it, for logout
        
        cookie_set($this->session_id_var_name, "$session_id/$session_key/" . date(DATETIME_MYSQL));
        return parent::logUserIn($user);
      } // if
    } // logUserIn
    
    /**
     * Log user out
     *
     * @param void
     * @return null
     */
    function logUserOut() {
      $delete = db_execute("DELETE FROM " . TABLE_PREFIX . 'user_sessions WHERE id = ?', $this->session_id);
      
      if($delete && !is_error($delete)) {
        cookie_unset($this->session_id_var_name);
        parent::logUserOut();
      } else {
        return $delete;
      } // if
    } // logUserOut
    
  }

?>