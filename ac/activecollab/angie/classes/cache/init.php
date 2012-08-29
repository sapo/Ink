<?php

  /**
   * Initialize cache library
   *
   * This file will initialize cache library - load classes and resources and 
   * register cache saving as a shutdown function
   * 
   * @package angie.library.cache
   */

  define('CACHE_LIB_PATH', ANGIE_PATH . '/classes/cache');
  
  require_once CACHE_LIB_PATH . '/Cache.class.php';
  require_once CACHE_LIB_PATH . '/backend/CacheBackend.class.php';
  
  /**
   * Use specific cache backend
   *
   * @param string $backend_name
   * @param array $params
   * @return CacheBackend
   */
  function cache_use_backend($backend_name, $params = null) {
    static $instance;
    if(defined('USE_CACHE') && USE_CACHE) {
      if($instance === null) {
        $instance =& Cache::instance();
      } // if
      return $instance->useBackend($backend_name, $params);
    } // if
    return null;
  } // cache_use_backend
  
  /**
   * Return value of specific variable from cache
   *
   * @param string $name
   * @return mixed
   */
  function cache_get($name) {
    static $instance;
    if(defined('USE_CACHE') && USE_CACHE) {
      if($instance === null) {
        $instance =& Cache::instance();
      } // if
      return $instance->backend->get($name);
    } // if
    return null;
  } // cache_get
  
  /**
   * Set value of specific variable
   *
   * $lifetime is number of seconds that cached value is considered value. By 
   * default it is 15 minutes. For testing purposes $lifetime is allowed to be 
   * negative value
   * 
   * @param string $name
   * @param mixed $value
   * @param integer $lifetime
   * @return boolean
   */
  function cache_set($name, $value) {
    static $instance;
    
    if(defined('USE_CACHE') && USE_CACHE) {
      if($instance === null) {
        $instance =& Cache::instance();
      } // if
      
      return $instance->backend->set($name, $value);
    } else {
      return null;
    } // if
  } // cache_set
  
  /**
   * Remove variable from backend
   * 
   * @param string $name
   * @return null
   */
  function cache_remove($name) {
    static $instance;
    if(defined('USE_CACHE') && USE_CACHE) {
      if($instance === null) {
        $instance =& Cache::instance();
      } // if
      return $instance->backend->remove($name);
    } else {
      return null;
    } // if
  } // cache_remove
  
  /**
   * Remove catche variables that match the pattern. * is used as a wildchar
   * 
   * @param string $pattern
   * @return null
   */
  function cache_remove_by_pattern($pattern) {
    static $instance;
    if(defined('USE_CACHE') && USE_CACHE) {
      if($instance === null) {
        $instance =& Cache::instance();
      } // if
      return $instance->backend->removeByPattern($pattern);
    } else {
      return null;
    } // if
  } // cache_remove_by_pattern
  
  /**
   * Save cache content to storage
   *
   * @param void
   * @return null
   */
  function cache_save() {
    static $instance;
    if(defined('USE_CACHE') && USE_CACHE) {
      if($instance === null) {
        $instance =& Cache::instance();
      } // if
      if(instance_of($instance->backend, 'CacheBackend')) {
        return $instance->backend->save();
      } // if
    } else {
      return null;
    } // if
  } // cache_save
  
  /**
   * Clear cache
   *
   * @param void
   * @return null
   */
  function cache_clear() {
    static $instance;
    if(defined('USE_CACHE') && USE_CACHE) {
      if($instance === null) {
        $instance =& Cache::instance();
      } // if
      return $instance->backend->clear();
    } // if
    
    return null;
  } // cache_clear
  
  // Make sure that cache is saved
  register_shutdown_function('cache_save');

?>