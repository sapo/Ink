<?php

  /**
   * File cache backend
   *
   * This backend saves cache data into a file
   * 
   * @package angie.library.cache
   * @subpackage backend
   */
  class FileCacheBackend extends CacheBackend {
    
    /**
     * Array of loaded data
     *
     * @var array
     */
    var $data = array();
    
    /**
     * Data that is set during the lifetime of this instance (new stuff)
     *
     * @var array
     */
    var $updated_data = array();
    
    /**
     * Path to the cache directory
     *
     * @var string
     */
    var $cache_dir;
  
    /**
     * Constructor
     *
     * @param array $params
     * @return FileCacheBackend
     */
    function __construct($params = null) {
      parent::__construct($params);
      
      $cache_dir = array_var($params, 'cache_dir');
      if(empty($cache_dir)) {
        $cache_dir = ENVIRONMENT_PATH . '/cache/';
      } // if
      
      $this->setCacheDir($cache_dir);
      
      clearstatcache();
    } // __construct
    
    /**
     * Get value for a given variable from cache and return it
     *
     * @param string $name
     * @return mixed
     */
    function get($name) {
      if(isset($this->data[$name])) {
        return $this->data[$name];
      } else {
        $path = $this->getFilePath($name);
        
        if(is_file($path)) {
          if((filemtime($path) + $this->lifetime) > $this->reference_time) {
            $this->data[$name] = require $path;
            return $this->data[$name];
          } else {
            unlink($path); // remove old file...
          } // if
        } // if
      } // if
      
      return null;
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
      $existing_value = $this->get($name);
      
      if($existing_value !== $value) {
        $this->remove($name);
        $this->data[$name] = $value;
        $this->updated_data[$name] = $value;
      } // if
    } // set
    
    /**
     * Remove variable from cache
     *
     * @param string $name
     * @return null
     */
    function remove($name) {
      if(isset($this->data[$name])) {
        unset($this->data[$name]);
      } // if
      
      if(isset($this->updated_data[$name])) {
        unset($this->updated_data[$name]);
      } // if
      
      $path = $this->getFilePath($name);
      if(is_file($path)) {
        unlink($path);
      } // if
    } // remove
    
    /**
     * Remove config options by pattern
     *
     * @param string $pattern
     * @return null
     */
    function removeByPattern($pattern) {
      $reg_expression = $this->preparePattern($pattern);
      
      clearstatcache();
      
      $d = dir($this->cache_dir);
      while(($entry = $d->read()) !== false) {
        if(!str_starts_with($entry, 'cch_')) {
          continue;
        } // if
        
        if(($pos = strpos($entry, '.')) !== false) {
          $cache_id = substr($entry, 4, $pos - 4);
        } else {
          $cache_id = $entry;
        } // if
        
        if(preg_match($reg_expression, $cache_id)) {
          $this->remove($cache_id);
        } // if
      } // if
      $d->close();
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
      clearstatcache();
      
      foreach($this->updated_data as $k => $v) {
        $path = $this->getFilePath($k);
        file_put_contents($path, '<?php return unserialize(' . var_export(serialize($v), true) . ') ?>');
      } // foreach
      
      $this->updated_data = array();
    } // save
    
    /**
     * Clear data from cache - drop everything
     *
     * @param void
     * @return null
     */
    function clear() {
      $this->data = array();
      
      clearstatcache();
      
      $d = dir($this->cache_dir);
      while(($entry = $d->read()) !== false) {
        if(!str_starts_with($entry, 'cch_')) {
          continue;
        } // if
        
        unlink($this->cache_dir . $entry);
      } // if
      $d->close();
    } // clear
    
    /**
     * Return filename for a given variable name
     *
     * @param string $filename
     * @return string
     */
    function getFilePath($filename) {
      return $this->cache_dir . "cch_$filename.php";
    } // getFilePath
    
    /**
     * Remove old files
     *
     * @param void
     * @return null
     */
    function cleanup() {
      $d = dir($this->cache_dir);
      
      $older_than = time() - $this->lifetime;
      
      while(($entry = $d->read()) !== false) {
        $path = $this->cache_dir . $entry;
        if(str_starts_with($entry, 'cch_') && (filectime($path) < $older_than)) {
          unlink($path);
        } // if
      } // if
    } // cleanup
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Set cache dir value
     *
     * @param string $value
     * @return null
     */
    function setCacheDir($value) {
      $this->cache_dir = with_slash($value);
    } // setCacheDir
  
  } // FileCacheBackend

?>