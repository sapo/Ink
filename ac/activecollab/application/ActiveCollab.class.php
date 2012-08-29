<?php

  /**
   * ActiveCollab application class
   * 
   * @package activeCollab
   */
  class ActiveCollab extends AngieApplication {
    
    /**
     * Array of available modules
     * 
     * @var array
     */
    var $modules = false;
    
    /**
     * activeCollab version
     *
     * @var string
     */
    var $version = '2.3';
    
    /**
     * API version
     *
     * @var string
     */
    var $api_version = '2.0';
    
    /**
     * Construct ActiveCollab object
     *
     * @param void
     * @return ActiveCollab
     */
    function __construct() {
      parent::__construct();
      
      require_once APPLICATION_PATH . '/modules/system/models/ApplicationObject.class.php';
      
      require_once APPLICATION_PATH . '/modules/system/models/modules/BaseModule.class.php';
      require_once APPLICATION_PATH . '/modules/system/models/modules/Module.class.php';
      require_once APPLICATION_PATH . '/modules/system/models/modules/BaseModules.class.php';
      require_once APPLICATION_PATH . '/modules/system/models/modules/Modules.class.php';
    } // __construct
    
    /**
     * Initialize module information
     *
     * @param void
     * @return null
     */
    function init_modules() {
      $modules = $this->getModules();
      
      if(is_foreachable($modules)) {
        foreach($this->getModules() as $module) {
          $path = $module->getPath();
          
          $this->smarty->plugins_dir[] = $path . '/helpers';
          require_once $path . '/init.php';
        } // foreach
        
        $this->router->loadByModules($modules);
        $this->events_manager->loadByModules($modules);
      } // if
    } // init_modules
    
    /**
     * Authenticate
     *
     * @param void
     * @return null
     */
    function authenticate() {
      $provider_class = AUTH_PROVIDER;
      use_auth_provider($provider_class);
      
      if(!class_exists($provider_class)) {
        use_error('ClassNotImplementedError');
        return new ClassNotImplementedError($provider_class);
      } // if
      
      $provider = new $provider_class();
      if(!instance_of($provider, 'AuthenticationProvider')) {
        return new InvalidInstanceError('provider', $provider, 'AuthenticationProvider');
      } // if
      
      $manager =& Authentication::instance($provider, false);
      
      $token = false;
      if(FORCE_QUERY_STRING) {
        if(ANGIE_QUERY_STRING) {
          $query_string_aprams = parse_string(ANGIE_QUERY_STRING);
          if(isset($query_string_aprams['token'])) {
            $token = $query_string_aprams['token'];
          } // if
        } // if
      } else {
        $token = isset($_GET['token']) ? $_GET['token'] : false;
      } // if
      
      // Handle token based authentication
      if($token !== false) {
        
        // Die if disabled or read-only with POST parameters
        if(API_STATUS == API_DISABLED || ((API_STATUS == API_READ_ONLY) && (count($_POST) > 0))) {
          header('HTTP/1.1 403 Forbidden');
          print "<h1>HTTP/1.1 403 Forbidden</h1>\n";
          if(API_STATUS == API_DISABLED) {
            print '<p>API is disabled</p>';
          } else {
            print '<p>API is read-only</p>';
          } // if
          die();
        } // if
        
        // Get token and auth_id (old and new API key formats are supported)
        if(strpos($token, '-') !== false) {
          list($auth_id, $token) = explode('-', $token);
        } else {
          $auth_id = array_var($_GET, 'auth_id');
        } // if
        
        $user = null;
        if($auth_id) {
          $user = Users::findById($auth_id);
        } // if
        
        if(instance_of($user, 'User') && ($user->getToken() == $token)) {
          $manager->provider->logUserIn($user, array('silent' => true));
          return true;
        } else {
          header('HTTP/1.1 403 Forbidden');
          print '<h1>HTTP/1.1 403 Forbidden</h1>';
          die();
        } // if
      } // if
      
      $manager->provider->initialize();
      return true;
    } // authenticate
    
    /**
     * Initialize locale settings based on logged in user
     *
     * @param void
     * @return null
     */
    function init_locale() {
      
      // Used when application is initialized from command line (we don't have
      // all the classes available)
      if(!class_exists('ConfigOptions') || !class_exists('UserConfigOptions')) {
        return true;
      } // if
      
      $logged_user =& get_logged_user();
      
      $language_id = null;
      if(instance_of($logged_user, 'User')) {
        if(LOCALIZATION_ENABLED) {
          $language_id = UserConfigOptions::getValue('language', $logged_user);
        } // if
        
        $format_date = UserConfigOptions::getValue('format_date', $logged_user);
        $format_time = UserConfigOptions::getValue('format_time', $logged_user);
      } else {
        if(LOCALIZATION_ENABLED) {
          $language_id = ConfigOptions::getValue('language');
        } // if
        
        $format_date = ConfigOptions::getValue('format_date');
        $format_time = ConfigOptions::getValue('format_time');
      } // if
      
      $language = new Language();
      
      // Now load languages
      if(LOCALIZATION_ENABLED && $language_id) {
        $language = Languages::findById($language_id);
        if(instance_of($language, 'Language')) {
          $current_locale = $language->getLocale();
          
          $GLOBALS['current_locale'] = $current_locale;
          $GLOBALS['current_locale_translations'] = array();
          
          if($current_locale != BUILT_IN_LOCALE) {
            setlocale(LC_ALL, $current_locale); // Set locale
            $GLOBALS['current_locale_translations'][$current_locale] = array();
            
            $language->loadTranslations($current_locale);
          } // if
        } // if
      } // if
      
      $this->smarty->assign('current_language', $language);
      
      define('USER_FORMAT_DATETIME', "$format_date $format_time");
      define('USER_FORMAT_DATE', $format_date);
      define('USER_FORMAT_TIME', $format_time);
      
      return true;
    } // init_locale
    
    /**
     * Returns true if copyright removal is part of the license
     *
     * @param void
     * @return boolean
     */
    function copyright_removed() {
      return defined('LICENSE_COPYRIGHT_REMOVED') && LICENSE_COPYRIGHT_REMOVED;
    } // copyright_removed
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Return all available modules
     *
     * @param void
     * @return array
     */
    function getModules() {
      if($this->modules === false) {
        $this->modules = Modules::findAll();
      } // if
      
      return $this->modules;
    } // getModules
    
  } // ActiveCollab

?>