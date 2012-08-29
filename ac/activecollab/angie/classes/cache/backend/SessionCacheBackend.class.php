<?php

  /**
   * Session cache backend
   *
   * This backend uses PHP-s session features to cache values. Beauty of this is 
   * that all the dirty work is handled by PHP. Major dissadvantage is that 
   * values are cacheable per session - one user and one continuous set of 
   * requests
   */
  class SessionCacheBackend extends CacheBackend {
    
    /**
     * $_SESSION field where we'll store data
     *
     * @var string
     */
    var $var_name = 'session_cache';
    
    /**
     * Construct cache backend
     * 
     * Session backend does not use any params
     *
     * @param array $params
     * @return CacheBackend
     */
    function __construct($params = null) {
      parent::__construct($params);
      
      if(!isset($_SESSION[$this->var_name])) {
        $_SESSION[$this->var_name] = array();
      } // if
    } // __construct
  
    /**
     * Get value for a given variable from cache and return it
     *
     * @param string $name
     * @return mixed
     */
    function get($name) {
      if(isset($_SESSION[$this->var_name][$name])) {
        list($value, $expiration_time) = $_SESSION[$this->var_name][$name];
        if($expiration_time) {
          if($expiration_time >= $this->reference_time) {
            return $value; // we got it and it is fresh enough
          } else {
            $this->remove($name); // cleanup
          } // if
        } // if
      } // if
      return null;
    } // get
    
    /**
     * Set value for a given variable
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    function set($name, $value) {
      $_SESSION[$this->var_name][$name] = array($value, $this->reference_time + $this->lifetime);
      return $value;
    } // set
    
    /**
     * Remove variable from cache
     *
     * @param string $name
     * @return null
     */
    function remove($name) {
      if(isset($_SESSION[$this->var_name][$name])) {
        unset($_SESSION[$this->var_name][$name]);
      } // if
    } // remove
    
    /**
     * Save data to persistant storage
     * 
     * Session cache reads and writes to $_SESSION variable and lets PHP handle 
     * the rest. Thats the whole beauty of this backend.
     *
     * @param void
     * @return null
     */
    function save() {
      return true;
    } // save
    
    /**
     * Clear data from cache - drop everything
     *
     * @param void
     * @return null
     */
    function clear() {
      $_SESSION[$this->var_name] = array();
      return true;
    } // clear
  
  } // SessionCacheBackend

?>