<?php

  /**
   * Abstract cache backend
   *
   * This class defines methods that need to be implemented by all cache backends
   * 
   * @package angie.library.cache
   * @subpackage backend
   */
  class CacheBackend extends AngieObject {
    
    /**
     * Reference time
     * 
     * Timestamp when this backend was constructed. We are keeping it for 
     * reference just so we don't need to call time every time we need to add a 
     * value to the cache
     *
     * @var integer
     */
    var $reference_time;
    
    /**
     * Cache lifetime (in seconds)
     *
     * @var integer
     */
    var $lifetime = 3600;
    
    /**
     * Construct cache backend
     *
     * @param array $params
     * @return CacheBackend
     */
    function __construct($params = null) {
      $this->reference_time = time();
      
      if(is_array($params)) {
        if(isset($params['lifetime'])) {
          $this->lifetime = $params['lifetime'];
        } // if
      } // if
    } // __construct
  
    /**
     * Get value for a given variable from cache and return it
     *
     * @param string $name
     * @return mixed
     */
    function get($name) {
      use_error('NotImplementedError');
      return new NotImplementedError('CacheBackend', 'get');
    } // get
    
    /**
     * Set value for a given variable
     * 
     * $lifetime is number of seconds that cached value is considered value. By 
     * default it is 15 minutes
     *
     * @param string $name
     * @param mixed $value
     * @param integer $lifetime
     * @return null
     */
    function set($name, $value) {
      use_error('NotImplementedError');
      return new NotImplementedError('CacheBackend', 'set');
    } // set
    
    /**
     * Remove variable from cache
     *
     * @param string $name
     * @return null
     */
    function remove($name) {
      use_error('NotImplementedError');
      return new NotImplementedError('CacheBackend', 'remove');
    } // remove
    
    /**
     * Remove config options by pattern
     *
     * @param string $pattern
     * @return null
     */
    function removeByPattern($pattern) {
      use_error('NotImplementedError');
      return new NotImplementedError('CacheBackend', 'removeByPattern');
    } // removeByPattern
    
    /**
     * Save data to persistant storage
     * 
     * This method is called when we need to save data to persistant storage. 
     * Some backends may decide not to use this but to write directly to the 
     * storage on set(), but in most cases that would be bad for performance 
     * (file system or database backends for example)
     *
     * @param void
     * @return null
     */
    function save() {
      use_error('NotImplementedError');
      return new NotImplementedError('CacheBackend', 'save');
    } // save
    
    /**
     * Clear data from cache - drop everything
     *
     * @param void
     * @return null
     */
    function clear() {
      use_error('NotImplementedError');
      return new NotImplementedError('CacheBackend', 'clear');
    } // clear
    
    /**
     * Cleanup
     * 
     * Some backends need to be periodically clean up and they should implement 
     * this method. If cron job is set up properly this method is called every 
     * hour
     *
     * @param void
     * @return null
     */
    function cleanup() {
      
    } // cleanup
    
    /**
     * Prepare pattern
     *
     * @param string $pattern
     * @return string
     */
    function preparePattern($pattern) {
      return '/^' . str_replace('*', '(.*)', $pattern) . '$/';
    } // preparePattern
  
  } // CacheBackend

?>