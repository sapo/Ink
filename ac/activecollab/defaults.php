<?php

  /**
   * Default configuration values
   */
  
  // URL-s
  if(!defined('URL_BASE')) {
  	define('URL_BASE', ROOT_URL . '/index.php');
  } // if
  if(!defined('ASSETS_URL')) {
    define('ASSETS_URL', ROOT_URL . '/assets');
  } // if
  define('APPLICATION_NAME', 'ActiveCollab');
  
  // Authentication provider
  if(!defined('AUTH_PROVIDER')) {
    define('AUTH_PROVIDER', 'BasicAuthenticationProvider');
  } // if
  
  // Search engine
  if(!defined('SEARCH_ENGINE')) {
    define('SEARCH_ENGINE', 'MysqlSearchEngine');
  } // if
  
  // Debug mode
  if(!defined('DEBUG')) {
    define('DEBUG', 1); // 0 -> none, 1 -> production, 2 -> development
  } // if
  
  if(!defined('DB_PERSIST')) {
    define('DB_PERSIST', true);
  } // if
  
  if(!defined('DB_CHARSET')) {
    define('DB_CHARSET', null);
  } // if
  
  if(!defined('DB_AUTO_RECONNECT')) {
    define('DB_AUTO_RECONNECT', 3); // Number of reconnection times if server drops connection in the middle of request
  } // if
  
  // Email charset and encoding
  if(!defined('EMAIL_ENCODING')) {
    define('EMAIL_ENCODING', '8bit');
  } // if
  
  if(!defined('EMAIL_CHARSET')) {
    define('EMAIL_CHARSET', 'utf-8'); // was utf8, need some testing
  } // if
  
  // Keep sessiion alive - ping interval in miliseconds
  // 300000 -> every 5 minutes, 0 for disable
  if(!defined('KEEP_ALIVE_INTERVAL')) {
  	define('KEEP_ALIVE_INTERVAL', 300000);
  } // if
  
  // Force query string for hosts that does not support 
  // PATH_INFO or make problems with it
  if(!defined('FORCE_QUERY_STRING')) {
    define('FORCE_QUERY_STRING', false);
  } // if
  
  if(!defined('PATH_INFO_THROUGH_QUERY_STRING')) {
    define('PATH_INFO_THROUGH_QUERY_STRING', false);
  } // if
  
  // Disable localization support
  if(!defined('LOCALIZATION_ENABLED')) {
    define('LOCALIZATION_ENABLED', false);
  } // if
  
  // Enable mass mailer
  if (!defined('MASS_MAILER_ENABLED')) {
    define('MASS_MAILER_ENABLED', true);
  } // if
  
  if(!defined('WARN_WHEN_JAVASCRIPT_IS_DISABLED')) {
    define('WARN_WHEN_JAVASCRIPT_IS_DISABLED', true);
  } // if
  
  if(!defined('PROTECT_SCHEDULED_TASKS')) {
    define('PROTECT_SCHEDULED_TASKS', false);
  } // if
  
  // Disable or enable API. Possible values:
  // 
  // 0 - disabled
  // 1 - enabled, but read only (default)
  // 2 - enabled, read and write
  if(!defined('API_STATUS')) {
    define('API_STATUS', 1);
  } // if
  
  if(!defined('PURIFY_HTML')) {
    define('PURIFY_HTML', true);
  } // if
  
  if(!defined('MAINTENANCE_MESSAGE')) {
    define('MAINTENANCE_MESSAGE', null);
  } // if
  
  // Thumbnails
  if(!defined('CREATE_THUMBNAILS')) {
    define('CREATE_THUMBNAILS', true); // create thumbnails for images
  } // if
  
  if(!defined('RESIZE_SMALLER_THAN')) {
    define('RESIZE_SMALLER_THAN', 524288); // resize images smaller than 500kb
  } // if
  
  // If this option is set to True users session ID will be refreshed every time
  // he or she visits a page
  if(!defined('ALWAYS_CHANGE_SESSION_ID')) {
    define('ALWAYS_CHANGE_SESSION_ID', false);
  } // if
  
  if(!defined('USER_SESSION_LIFETIME')) {
    define('USER_SESSION_LIFETIME', 1800); // 30 minutes
  } // if
  
  // if this option is set to true, mailbox manager will use some of custom functions
  // to handle retrieveing emails
  if(!defined('FAIL_SAFE_IMAP_FUNCTIONS')) {
    define('FAIL_SAFE_IMAP_FUNCTIONS', false);
  } // if
  
  if(!defined('FAIL_SAFE_IMAP_ATTACHMENT_SIZE_MAX')) {
    define('FAIL_SAFE_IMAP_ATTACHMENT_SIZE_MAX', 512000);
  } // if
  
  if(!defined('COMPRESS_ASSET_REQUESTS')) {
    define('COMPRESS_ASSET_REQUESTS', true);
  } // if
  
  // Number format
  define('NUMBER_FORMAT_DEC_SEPARATOR', '.');
  define('NUMBER_FORMAT_THOUSANDS_SEPARATOR', '');
  
  // Cache
  define('USE_CACHE', true);
  define('CACHE_BACKEND', 'FileCacheBackend');
  define('CACHE_LIFETIME', 7200);
  
  // Cookie...
  define('USE_COOKIES', true);
  
  if(!defined('COOKIE_DOMAIN')) {
    $parts = parse_url(ROOT_URL);
    if(is_array($parts) && isset($parts['host'])) {
      define('COOKIE_DOMAIN', $parts['host']);
    } else {
      define('COOKIE_DOMAIN', '');
    } // if
  } // if
  
  define('COOKIE_PATH', '/');
  define('COOKIE_SECURE', 0);
  define('COOKIE_PREFIX', 'ac');
  
  // Flash
  define('USE_FLASH', true);
  
  // MVC
  define('DEFAULT_MODULE', 'system');
  define('DEFAULT_CONTROLLER', 'dashboard');
  define('DEFAULT_ACTION', 'index');
  define('DEFAULT_FORMAT', 'html');
  
  // Default date / time formats
  //
  // Formats can be overriden with constants with same name that start with 
  // USER_ (USER_FORMAT_DATE will override FORMAT_DATE)
  if(!defined('FORMAT_DATETIME')) {
    define('FORMAT_DATETIME', '%b %e. %Y, %I:%M %p');
  } // if
  if(!defined('FORMAT_DATE')) {
    define('FORMAT_DATE', '%b %e. %Y');
  } // if
  if(!defined('FORMAT_TIME')) {
    define('FORMAT_TIME', '%I:%M %p');
  } // if
  
  // Read environment name from environment path
  define('ENVIRONMENT', substr(ENVIRONMENT_PATH, strrpos(ENVIRONMENT_PATH, '/') + 1));
  
  define('APPLICATION_PATH', ROOT . '/application');
  define('DEVELOPMENT_PATH', ROOT . '/development');
  
  if(!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', ENVIRONMENT_PATH . '/upload');
  } // if
  
  define('CUSTOM_PATH', ENVIRONMENT_PATH . '/custom');
  define('LOCALIZATION_PATH', CUSTOM_PATH . '/localization');
  define('THUMBNAILS_PATH', ENVIRONMENT_PATH . '/thumbnails');
  define('WORK_PATH', ENVIRONMENT_PATH . '/work');
  
  if (!defined('PROJECT_EXPORT_PATH')) {
    define('PROJECT_EXPORT_PATH', WORK_PATH . '/export');
  } // if
  
?>