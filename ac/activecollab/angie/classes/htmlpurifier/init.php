<?php

  /**
   * HTML Purifier initialization file
   *
   * @package angie.library.purifier
   */
  
  define('HTML_PURIFIER_LIB_PATH', ANGIE_PATH . '/classes/htmlpurifier');
  
  /**
   * Purify HTML
   * 
   * This function will initialize HTML Purifier and run $html through it.
   *
   * @param string $html
   * @return string
   */
  function purify_html($html) {
    if(defined('PURIFY_HTML') && PURIFY_HTML) {
      require_once HTML_PURIFIER_LIB_PATH . '/HTMLPurifier.php';
      
      $config = HTMLPurifier_Config::createDefault();
      $config->set('Cache', 'SerializerPath', ENVIRONMENT_PATH . '/cache');
      
      $purifier = new HTMLPurifier($config);
      return $purifier->purify($html);
    } else {
      return $html;
    } // if
  } // purify_html

?>