<?php

  /**
   * Cache class
   *
   * This class to load and provide interface to specific cache backend
   * 
   * @package angie.library.cache
   */
  class Cache extends AngieObject {
    
    /**
     * Cache backend used to store cached values
     *
     * @var CacheBackend
     */
    var $backend;
    
    /**
     * Load, initialize and use specific backend
     * 
     * @param string $backend_name
     * @param array $params
     * @return boolean
     * @throws InvalidParamError if backend $backend_name does not exist
     */
    function &useBackend($backend_name, $params = null) {
      if(empty($backend_name)) {
        $this->backend = null;
      } else {
        $backend_path = CACHE_LIB_PATH . "/backend/$backend_name.class.php";
        if(is_file($backend_path)) {
          require_once $backend_path;
          $this->backend = new $backend_name($params);
        } else {
          return new InvalidParamError('backend_name', $backend_name, "There is no '$backend_name' cache backend. Expected path: $backend_path", true);
        } // if
      } // if
      
      return $this->backend;
    } // useBackend
  
    /**
     * Return cache instance
     *
     * @param void
     * @return Cache
     */
    function &instance() {
      static $instance;
      if(!instance_of($instance, 'Cache')) {
        $instance = new Cache();
      } // if
      return $instance;
    } // instance
  
  } // Cache

?>