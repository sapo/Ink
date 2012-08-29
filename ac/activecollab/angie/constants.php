<?php

  /**
   * General purpose and compatibility constants
   *
   * @package angie
   */

  // Some nice to have regexps
  define('EMAIL_FORMAT', "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i");
  define('URL_FORMAT', '/^(http|https):\/\/(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/');
  define('IP_URL_FORMAT', "/^(http|https):\/\/((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])((:[0-9]{1,5})?\/.*)?$/");
  define('IP_FORMAT', "/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])$/");

  define('DATE_MYSQL', 'Y-m-d');
  define('DATETIME_MYSQL', 'Y-m-d H:i:s');
  define('EMPTY_DATE', '0000-00-00');
  define('EMPTY_DATETIME', '0000-00-00 00:00:00');
  
  define('ANY_FIELD', '-- any --'); // used in model validation
  
  // Debug levels
  define('DEBUG_OFF', 0); // turn off logger and error display
  define('DEBUG_PRODUCTION', 1); // log everything but hide errors from users
  define('DEBUG_DEVELOPMENT', 2); // show and log everything
 
  // Comparision operators
  define('COMPARE_LT', '<');
  define('COMPARE_LE', '<=');
  define('COMPARE_GT', '>');
  define('COMPARE_GE', '>=');
  define('COMPARE_EQ', '==');
  define('COMPARE_NE', '!=');
  
  define('FORMAT_HTML', 'html');
  define('FORMAT_XML', 'xml');
  define('FORMAT_JSON', 'json');
  define('FORMAT_ICAL', 'ical');
  
  define('FEED_RSS', 'application/rss+xml');
  define('FEED_ATOM', 'application/atom+xml');
  
  // Compatibility constants (available since PHP 5.1.1). This constants are 
  // taken from PHP_Compat PEAR package
  if(!defined('DATE_ATOM')) {
    define('DATE_ATOM',    'Y-m-d\TH:i:sO');
  } // if
  if(!defined('DATE_COOKIE')) {
    define('DATE_COOKIE',  'D, d M Y H:i:s T');
  } // if
  if(!defined('DATE_ISO8601')) {
    define('DATE_ISO8601', 'Y-m-d\TH:i:sO');
  } // if
  if(!defined('DATE_RFC822')) {
    define('DATE_RFC822',  'D, d M Y H:i:s T');
  } // if
  if(!defined('DATE_RFC850')) {
    define('DATE_RFC850',  'l, d-M-y H:i:s T');
  } // if
  if(!defined('DATE_RFC1036')) {
    define('DATE_RFC1036', 'l, d-M-y H:i:s T');
  } // if
  if(!defined('DATE_RFC1123')) {
    define('DATE_RFC1123', 'D, d M Y H:i:s T');
  } // if
  if(!defined('DATE_RFC2822')) {
    define('DATE_RFC2822', 'D, d M Y H:i:s O');
  } // if
  if(!defined('DATE_RSS')) {
    define('DATE_RSS',     'D, d M Y H:i:s T');
  } // if
  if(!defined('DATE_W3C')) {
    define('DATE_W3C',     'Y-m-d\TH:i:sO');
  } // if
  
  // HTTP errors
  define('HTTP_ERR_BAD_REQUEST', 400);
  define('HTTP_ERR_UNAUTHORIZED', 401);
  define('HTTP_ERR_FORBIDDEN', 403);
  define('HTTP_ERR_NOT_FOUND', 404);
  define('HTTP_ERR_INVALID_PROPERTIES', 409);
  define('HTTP_ERR_OPERATION_FAILED', 500);
  define('HTTP_ERR_CONFLICT', 409);
    
  // Directory separator
  if(!defined('DIRECTORY_SEPARATOR')) {
    define('DIRECTORY_SEPARATOR', strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? '\\' : '/');
  } // if

?>