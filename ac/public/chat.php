<?php

  /**
   * Chat execution class
   * 
   * This file is executed every second in order to refresh view chat page. It 
   * is made to use as less resources as possible
   *
   * @package activeCollab.modules.chat
   * @subpackage models
   */
  
  @session_start();

  define('MYSQL_DATE', 'Y-m-d H:i:s');
  
  /**
   * Equivalent to htmlspecialchars(), but allows &#[0-9]+ (for unicode)
   * 
   * This function was taken from punBB codebase <http://www.punbb.org/>
   *
   * @param string $str
   * @return string
   */
  function clean($str) {
    $str = preg_replace('/&(?!#(?:[0-9]+|x[0-9A-F]+);?)/si', '&amp;', $str);
  	$str = str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $str);
  
  	return $str;
  } // clean
  
  /**
   * Escape string that is goint into the database
   *
   * @param mixed $data
   * @return string
   */
  function escape_string($data) {
    return "'" . mysql_real_escape_string($data) . "'";
  } // escape_string
  
  /**
   * Show var dump. pre_var_dump() is used for testing only!
   *
   * @param mixed $var
   * @return null
   */
  function pre_var_dump($var) {
    print "<pre style=\"text-align: left\">\n";
    
    ob_start();
    var_dump($var);
    print clean(ob_get_clean());
    
    print "</pre>\n";
  } // pre_var_dump
  
  /**
   * Chat interface class
   * 
   * @package activeCollab.modules.chat
   * @subpackage models
   */
  class Chat {
    
    /**
     * Current timestamp
     *
     * @var integer
     */
    var $now;
    
    /**
     * User session id
     * 
     * @var string
     */
    var $session_id;
    
    /**
     * Room id
     * 
     * @var integer
     */
    var $room_id;
    
    /**
     * Messages to add
     *
     * @var string
     */
    var $messages;
    
    /**
     * Cached last message id
     *
     * @var integer
     */
    var $last_message_id;
    
    /**
     * ID of last file
     *
     * @var integer
     */
    var $last_file_id;
    
    /**
     * Array of active users shown on page
     *
     * @var array
     */
    var $user_ids = array();
    
    /**
     * Chached user
     *
     * @var array
     */
    var $user = false;
    
    /**
     * Database connection link
     *
     * @var resource
     */
    var $link;
    
    /**
     * Construct
     *
     * @param Array $data
     * @return null
     */
    function Chat($data) {
      $this->now = time();
      
      require_once '../config/config.php';
      
      $this->link = DB_PERSIST ? mysql_pconnect(DB_HOST, DB_USER, DB_PASS) : mysql_connect(DB_HOST, DB_USER, DB_PASS);
      
      if($this->link) {
        if(!mysql_select_db(DB_NAME, $this->link)) {
          $this->error('Failed to select database');
        } // if
      } else {
        $this->error('Failed to connect to database');
      } // if
      
      if(defined('DB_CHARSET') && DB_CHARSET) {
        mysql_query('SET NAMES ' . mysql_escape_string(DB_CHARSET), $this->link);
      } // if
      
      $this->room_id         = (integer) $data['room_id'];
      $this->session_id      = trim($data['session_id']);
      $this->last_message_id = (integer) $data['last_message_id'];
      $this->last_file_id    = (integer) $data['last_file_id'];
      $this->user_ids        = isset($data['user_ids']) && trim($data['user_ids']) ? explode(',', $data['user_ids']) : array();
      
      $this->messages = array();
      foreach($data as $k => $v) {
        if(substr($k, 0, 7) == 'message') {
          $this->messages[] = $v;
        } // if
      } // foreach
      
      // Load user
      $users_table = TABLE_PREFIX . 'users';
      $chat_users_table = TABLE_PREFIX . 'chat_users';
      
      $room_users = mysql_query("SELECT $users_table.id, $users_table.first_name, $users_table.last_name, $chat_users_table.session_id, $chat_users_table.last_activity_on FROM $users_table, $chat_users_table  WHERE $chat_users_table.user_id = $users_table.id AND $chat_users_table.session_id = " . escape_string($this->session_id), $this->link);
      if(mysql_num_rows($room_users)) {
        $this->user = mysql_fetch_assoc($room_users);
        
        // Update last activity...
        $last_activity_on = strtotime($this->user['last_activity_on']);
        if($this->now > ($last_activity_on + 300)) {
          mysql_query($sql = "UPDATE $chat_users_table SET last_activity_on = NOW(), is_active = '1' WHERE user_id = " . escape_string($this->user['id']) . " AND room_id = " . escape_string($this->room_id), $this->link);
        } // if
      } else {
        header("HTTP/1.1 401 Unauthorized");
        die();
      } // if
      
    } // construct
    
    /**
     * Add text messages
     *
     * @param void
     * @return null
     */
    function addTextMessages() {
      if($this->canAdd()) {
      	$messages_table = TABLE_PREFIX . 'chat_messages';
      	if(is_array($this->messages)) {
          foreach($this->messages as $message) {
            mysql_query(sprintf("INSERT INTO %s (room_id, content, type, created_on, created_by_id, created_by_name) VALUES (%s, %s, 'message', %s, %s, %s)", 
              $messages_table, 
              escape_string($this->room_id), 
              escape_string(clean(trim($message))), 
              escape_string(gmdate("Y-m-d H:i:s", time())), 
              escape_string($this->user['id']), 
              escape_string($this->user['first_name'] . ' ' . $this->user['last_name'])
            ));
          } // foreach
      	} // if
      	
      	return true;
      } else {
      	return false;
      } // if
    } // addTextMessages
    
    /**
     * Return new messages
     *
     * @param void
     * @return null
     */
    function getNewMessages() {
    	$messages_table = TABLE_PREFIX . 'chat_messages';
    	
    	$cond = '((type = ' . escape_string('system') . ') OR (type = ' . escape_string('message') . ' AND created_by_id != ' . escape_string($this->user['id']) . '))';
    	if($this->last_message_id) {
    	  $messages = mysql_query("SELECT * FROM $messages_table WHERE room_id = " . escape_string($this->room_id) . " AND id > " . escape_string($this->last_message_id) . " AND " . $cond);
    	} else {
    	  $messages = mysql_query("SELECT * FROM $messages_table WHERE room_id = " . escape_string($this->room_id) . " AND " . $cond);
    	} // if
      
      $new_messages = array();
      while($row = mysql_fetch_array($messages)) {
        $row['user_avatar'] = $this->getUserAvatarUrl($row['created_by_id'], true);
        $new_messages[] = $row;
      } // if
      
      return $new_messages;
    } // getNewMessages
    
    /**
     * Return a list of files since our last check
     *
     * @param void
     * @return array
     */
    function getNewFiles() {
      if($this->last_file_id) {
        $result = mysql_query('SELECT id, name, type, size FROM ' . TABLE_PREFIX . 'chat_files WHERE room_id = ' . escape_string($this->room_id) . ' AND id > ' . escape_string($this->last_file_id) . ' ORDER BY created_on DESC LIMIT 0, 3');
      } else {
        $result = mysql_query('SELECT id, name, type, size FROM ' . TABLE_PREFIX . 'chat_files WHERE room_id = ' . escape_string($this->room_id) . ' ORDER BY created_on DESC LIMIT 0, 3');
      } // if
      
      $files = array();
      while($row = mysql_fetch_assoc($result)) {
        $row['id'] = (integer) $row['id'];
        $row['size'] = (integer) $row['size'];
        $row['url'] = $this->getFileUrl($row['id']);
        
        $file_size_data = array(
          'TB' => 1099511627776,
          'GB' => 1073741824,
          'MB' => 1048576,
          'kb' => 1024,
        );
        
        $size_in_bytes = (integer) $row['size'];
        
        $row['size'] = $size_in_bytes . 'b';
        foreach($file_size_data as $unit => $bytes) {
          $in_unit = $size_in_bytes / $bytes;
          if($in_unit > 0.9) {
            $row['size'] = number_format($in_unit, 2, NUMBER_FORMAT_DEC_SEPARATOR, NUMBER_FORMAT_THOUSANDS_SEPARATOR) . $unit;
            break;
          } // if
        } // foreach
        
        $files[] = $row;
      } // if
      
      return count($files) ? array_reverse($files) : null;
    } // getNewFiles
    
    /**
     * This function will return array of users that were active in last 30 
     * minutes
     *
     * @param void
     * @return array
     */
    function getUsers() {      
      $chat_users_table = TABLE_PREFIX . 'chat_users';
      $users_table = TABLE_PREFIX . 'users';
      
      $user_ids = array();
      $result = mysql_query("SELECT user_id FROM $chat_users_table WHERE room_id = '" . $this->room_id . "' AND is_active > '0' AND last_activity_on > " . escape_string(date(MYSQL_DATE, $this->now - 1800)));
      while($row = mysql_fetch_assoc($result)) {
        $user_ids[] = (integer) $row['user_id'];
      } // while
      
      if(count($user_ids)) {
        if(count($user_ids) == count($this->user_ids)) {
          $all_loaded = true;
          foreach($user_ids as $user_id) {
            if(!in_array($user_id, $this->user_ids)) {
              $all_loaded = false;
              break;
            } // if
          } // foreach
          
          // We have all the users already
          if($all_loaded === true) {
            return null;
          } // if
        } // if
        
        // We need to load new list of users
        $users = array();
        $result = mysql_query("SELECT $users_table.id, $users_table.first_name, $users_table.last_name, UNIX_TIMESTAMP($chat_users_table.last_activity_on) AS 'last_activity_on' FROM $users_table, $chat_users_table WHERE $users_table.id = $chat_users_table.user_id AND $chat_users_table.is_active = '1' AND $chat_users_table.last_activity_on > " . escape_string(date(MYSQL_DATE, $this->now - 1800)) . " ORDER BY $users_table.first_name, $users_table.last_name");
        while($row = mysql_fetch_assoc($result)) {
          $row['id'] = (integer) $row['id'];
          $row['display_name'] = $row['first_name'] . ' ' . $row['last_name'];
          unset($row['first_name']);
          unset($row['last_name']);
          
          $users[$row['id']] = $row;
        } // while
        
        return count($users) ? $users : null;
        
      } else {
        return null;
      } // if
    } // getUsers
    
    /**
     * Return URL of specific file
     *
     * @param integer $file_id
     * @return string
     */
    function getFileUrl($file_id) {
      return URL_BASE . '/chat/' . $this->room_id . '/file/' . $file_id;
    } // getFileUrl
    
    /**
     * Get Avatar Path 
     *
     * @param boolean $large
     * @return string
     */
    function getUserAvatarPath($user_id,$large = false) {
      $size = $large ? '40x40' : '16x16';
      if ($large) {
        return ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/avatars/' . $user_id . ".$size.jpg";      	
      } else {
        return ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/avatars/' . $user_id . ".$size.jpg";
      } // if
    } // getAvatarPath
    
    /**
     * Get User Avatar URL
     *
     * @param boolean $large
     * @return string
     */
    function getUserAvatarUrl($user_id, $large = false) {
      $size = $large ? '40x40' : '16x16';
      if(is_file($this->getUserAvatarPath($user_id, $large))) {
        return ROOT_URL . '/avatars/' . $user_id . ".$size.jpg";
      } else {
        return ROOT_URL . "/avatars/default.$size.gif";
      } // if
    } // getAvatarUrl
    
    /**
     * Return true if user can add message
     *
     * @param string $session_ids
     * @return boolean
     */
    function canAdd() {
      return is_array($this->user);
    } // canAdd
    
    /**
     * Throw an error message and provide proper HTTP code
     *
     * @param string $message
     * @return null
     */
    function error($message) {
      header('HTTP/1.1 500 ' . clean($message));
      print '<p>' . clean($message) . '</p>';
      die();
    } // error
    
  }
  
  // ---------------------------------------------------
  //  Let's do some maagic
  // ---------------------------------------------------

  define('ENVIRONMENT_PATH', realpath(dirname(__FILE__) . '/..'));
  define('ENVIRONMENT', substr(ENVIRONMENT_PATH, strrpos(ENVIRONMENT_PATH, '/') + 1));
  
  $chat = new Chat($_POST);
  $chat->addTextMessages();
  
  header('Content-Type: text/html; charset=utf-8');
  
  // Print new message command if any
  $new_messages = $chat->getNewMessages();
  if(is_array($new_messages)) {
    foreach($new_messages as $new_message) {
      if($new_message['type'] == 'message') {
        print 'App.Chat.add_message(' . var_export($new_message['content'], true) . ', {
          author: {
            "id" : ' . $new_message['created_by_id'] . ',
            "display" : ' . var_export($new_message['created_by_name'], true) . ',
            "avatar" : ' . var_export($chat->getUserAvatarUrl($new_message['created_by_id'], true), true) . '
          },
          message_id : ' . $new_message['id'] . ',
          created_on : ' . var_export($new_message['created_on'], true) . ',
          silent : true
        });';
      } else {
         print 'App.Chat.add_message(' . var_export($new_message['content'], true) . ', {
           message_id : ' . $new_message['id'] . ',
           created_on : ' . var_export($new_message['created_on'], true) . ',
           silent : true
         });';
      } // if
      print "\n";
    } // foreach
  } // if
  
  $new_files = $chat->getNewFiles();
  if(is_array($new_files)) {
    $generated_files = array();
    foreach($new_files as $new_file) {
      $blocks = array();
      foreach($new_file as $k => $v) {
        $blocks[] = $k . ' : ' . var_export($v, true);
      } // foreach
      $generated_files[] = $new_file['id'] . ' : { ' . implode(",\n", $blocks) . '}';
    } // foreach
    
    print 'App.Chat.refresh_files({' . implode(",\n", $generated_files) . "});";
  } // if
  
  // Print new users commands if any
  $new_users = $chat->getUsers();
  if(is_array($new_users)) {
    $generated_users = array();
    foreach($new_users as $new_user) {
      $blocks = array();
      foreach($new_user as $k => $v) {
        $blocks[] = $k . ' : ' . var_export($v, true);
      } // foreach
      $generated_users[] = $new_user['id'] . ' : { ' . implode(",\n", $blocks) . '}';
    } // foreach
    
    print 'App.Chat.refresh_users({' . implode(",\n", $generated_users) . "});";
  } // if
  
?>