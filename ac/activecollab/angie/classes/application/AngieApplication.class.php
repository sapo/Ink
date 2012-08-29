<?php

  /**
   * Base angie application class
   *
   * This class implements most of the Angie application initialization routines. 
   * We figured that things were scattered to much in previous setup so we 
   * decided to move everything into one class that can be overriden if user 
   * finds need to change initialization process.
   * 
   * @package angie.library.application
   */
  class AngieApplication extends AngieObject {
    
    /**
     * Cached instance of router
     *
     * @var Router
     */
    var $router;
    
    /**
     * Cached instance of event manager
     * 
     * @var EventsManager
     */
    var $events_manager;
    
    /**
     * Smarty instance
     *
     * @var Smarty
     */
    var $smarty;
    
    /**
     * Construct Angie application instance
     *
     * @param void
     * @return AngieApplication
     */
    function __construct() {
      $this->router =& Router::instance();
      $this->events_manager =& EventsManager::instance();
      $this->smarty =& Smarty::instance();
    } // __construct
    
    // ---------------------------------------------------
    //  Public methods
    // ---------------------------------------------------
    
    /**
     * Prepare application environment
     * 
     * Settings:
     * 
     * - db_connect - connect to database
     * - construct_smarty - construct smarty
     * - init_modules - initialize modules
     * - use_cache - use cache
     *
     * @param array $settings
     * @return null
     */
    function prepare($settings) {
      foreach($settings as $setting => $value) {
        if($value) {
          $this->$setting();
        } // if
      } // foreach
    } // prepare
    
    /**
     * Initialize application resoruces
     *
     * @param void
     * @return null
     */
    function init() {
      event_trigger('on_before_init');
      require APPLICATION_PATH . '/init.php';
      event_trigger('on_after_init');
    } // init
    
    /**
     * Get and handle HTTP request
     *
     * @param void
     * @return null
     */
    function handleHttpRequest() {
      $request = $this->router->match(ANGIE_PATH_INFO, ANGIE_QUERY_STRING);
      
      if(is_error($request)) {
        handle_fatal_error($request);
      } else {
        $execute =& execute_action($request);
        if(is_error($execute)) {
          handle_fatal_error($execute);
        } // if
      } // if
    } // handleHttpRequest
    
    /**
     * Called on application shutdown
     *
     * @param void
     * @return null
     */
    function shutdown() {
      event_trigger('on_shutdown');
    } // shutdown
    
    // ---------------------------------------------------
    //  Util methods
    // ---------------------------------------------------
    
    /**
     * Initialize application resources
     *
     * @param void
     * @return null
     */
    function initialize_resources() {
      if(defined('USE_COOKIES') && USE_COOKIES) {
        Cookies::init(COOKIE_PREFIX, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE);
      } // if
      if(defined('USE_FLASH') && USE_FLASH) {
        Flash::init();
      } // if
      if(defined('USE_CACHE') && USE_CACHE) {
        cache_use_backend(CACHE_BACKEND, array('lifetime' => CACHE_LIFETIME));
      } // if
    } // initialize_resources
    
    /**
     * Connect to database with parameters provided in config file
     *
     * @param void
     * @return boolean
     */
    function connect_to_database() {
      $database_connect = db_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PERSIST, DB_CHARSET);
      if(is_error($database_connect)) {
        if(DEBUG) {
          dump_error($database_connect);
        } else {
          trigger_error('Failed to connect to database');
        } // if
      } // if
    } // connect_to_database
    
    /**
     * Construct and set up main Smarty instance
     *
     * @param void
     * @return null
     */
    function initialize_smarty() {
      $smarty_instance =& Smarty::instance();
  
      $smarty_instance->compile_dir = ENVIRONMENT_PATH . '/compile/';
      $smarty_instance->cache_dir   = ENVIRONMENT_PATH . '/cache/templates/';
      
      $smarty_instance->debugging     = false;
      $smarty_instance->compile_check = true;
      $smarty_instance->force_compile = false;
    } // initializeSmarty
    
    /**
     * Load hooks definitions after modules are inited
     *
     * @param void
     * @return null
     */
    function load_hooks() {
      require_once ANGIE_PATH . '/hooks.php';
    } // load_hooks
    
    /**
     * Initialize authentication
     * 
     * First we get authentication provider and then we create authentication 
     * manager instance...
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
        return new InvalidInstanceError('provider', $provide, 'AuthenticationProvider');
      } // if
      
      $manager =& Authentication::instance($provider);
    } // authenticate
    
  } // AngieApplication

?>