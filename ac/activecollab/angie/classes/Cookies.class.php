<?php

  /**
  * Cookie service class
  *
  * Cookie service maps $_COOKIE and prvides simple methods for setting and 
  * getting cookie data. If adds prefixes to variable name to provide just to 
  * make sure that we are using correct data
  *
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class Cookies {
    
    /**
    * Cookie path
    *
    * @var string
    */
    var $path;
    
    /**
    * Cookie domain
    *
    * @var string
    */
    var $domain;
    
    /**
    * User HTTPS if available for cookies    
    *
    * @var boolean
    */
    var $secure = false;
  
    /**
    * Cookie prefix
    *
    * @var string
    */
    var $prefix;
    
    /**
    * Expiration time, in seconds
    *
    * @var integer
    */
    var $expirationTime = 1209600;
    
    /**
    * Init cookie service
    *
    * @param string $prefix Variable prefix
    * @param string $path Cookie path
    * @param string $domain Cookie domain
    * @param boolean $secure Use HTTPS for cookies if available
    * @param integer $exp_time Expiration time
    * @return null
    */
    function init($prefix, $path, $domain, $secure, $exp_time = null) {
      if(isset($this) && instance_of($this, 'Cookies')) {
        $this->setPrefix($prefix);
        $this->setPath($path);
        $this->setDomain($domain);
        $this->setSecure($secure);
        if(!is_null($exp_time)) {
          $this->setExpirationTime($exp_time);
        } // if
      } else {
        $instance =& Cookies::instance();
        $instance->init($prefix, $path, $domain, $secure, $exp_time);
      } // if
    } // init
    
    /**
    * Return variable value from cookie
    *
    * @param string $name Variable name
    * @return mixed
    */
    function getVariable($name) {
      if(isset($this) && instance_of($this, 'Cookies')) {
        $var_name = $this->getVariableName($name);
        return isset($_COOKIE[$var_name]) ? $_COOKIE[$var_name] : null;
      } else {
        $instance =& Cookies::instance();
        return $instance->getVariable($name);
      } // if
    } // getVariable
    
    /**
    * Set cookie variable
    *
    * @param string $name Variable name, without prefix
    * @param mixed $value Value that need to be set
    * @param integer $expiration_time Expiration time, in seconds
    * @return null
    */
    function setVariable($name, $value, $expiration_time = null) {
      if(isset($this) && instance_of($this, 'Cookies')) {
        $name = $this->getVariableName($name);
        $secure = $this->getSecure() ? 1 : 0;
        
        if(is_null($expiration_time) || ((integer) $expiration_time < 1)) {
          $exp_time = time() + $this->getExpirationTime();
        } else {
          $exp_time = time() + (integer) $expiration_time;
        } // if
        
        return setcookie($name, $value, $exp_time, $this->getPath(), $this->getDomain(), $secure);
      } else {
        $instance =& Cookies::instance();
        return $instance->setVariable($name, $value, $expiration_time);
      } // if
    } // setVariable
    
    /**
    * Unset cookie variable
    *
    * @param string $name Cookie name
    * @return null
    */
    function unsetVariable($name) {
      if(isset($this) && instance_of($this, 'Cookies')) {
        $var_name = $this->getVariableName($name);
        $this->setVariable($name, null);
        $_COOKIE[$var_name] = null;
      } else {
        $instance =& Cookies::instance();
        return $instance->unsetVariable($name);
      } // if
    } // unsetVariable
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
    * Put prefix in front of variable name if available
    *
    * @param string $name Original name
    * @return string
    */
    function getVariableName($name) {
      return trim($this->getPrefix()) == '' ? $name : trim($this->getPrefix()) . '_' . trim($name);
    } // getVariableName
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Return cookie path
    *
    * @param void
    * @return string
    */
    function getPath() {
      return $this->path;
    } // getPath
    
    /**
    * Set cookie path
    *
    * @param string $value New value
    * @return null
    */
    function setPath($value) {
      $this->path = trim($value);
    } // setPath
    
    /**
    * Return cookie domain
    *
    * @param void
    * @return string
    */
    function getDomain() {
      return $this->domain;
    } // getDomain
    
    /**
    * Set cookie domain
    *
    * @param string $value New value
    * @return null
    */
    function setDomain($value) {
      $this->domain = trim($value);
    } // setDomain
    
    /**
    * Return secure value
    *
    * @param void
    * @return boolean
    */
    function getSecure() {
      return $this->secure;
    } // getSecure
    
    /**
    * Set cookie secure flag
    *
    * @param boolean $value New value
    * @return null
    */
    function setSecure($value) {
      $this->secure = (boolean) $value;
    } // setSecure
    
    /**
    * Return cookie prefix
    *
    * @param void
    * @return string
    */
    function getPrefix() {
      return $this->prefix;
    } // getPrefix
    
    /**
    * Set cookie prefix
    *
    * @param string $value New value
    * @return null
    */
    function setPrefix($value) {
      $this->prefix = trim($value);
    } // setPrefix
    
    /**
    * Return cookie expiration time, in seconds
    *
    * @param void
    * @return integer
    */
    function getExpirationTime() {
      return $this->expirationTime;
    } // getExpirationTime
    
    /**
    * Set cookie expiration time
    *
    * @param integer $value Number of seconds
    * @return null
    */
    function setExpirationTime($value) {
      if((integer) $value > 0) $this->expirationTime = (integer) $value;
    } // setExpirationTime
    
    /**
    * Return cookie service instance
    *
    * @param void
    * @return Cookies
    */
    function & instance() {
      static $instance;
      if(!instance_of($instance, 'Cookies')) {
        $instance = new Cookies();
      } // if
      return $instance;
    } // instance
    
  } // Cookies
  
  // ===================================================================
  //  Shortcut methods
  // ===================================================================
  
  /**
  * Shortcut to Cookies::getVariable method
  *
  * @param string $name Variable name
  * @return mixed
  */
  function cookie_get($name) {
    $instance =& Cookies::instance();
    return $instance->getVariable($name);
  } // cookie_get
  
  /**
  * Shortcut to Cookies::setVariable method
  *
  * @param string $name Variable name
  * @param mixed $value Value that need to be set
  * @param integer $expiration_time Expiration time, in seconds
  * @return mixed
  */
  function cookie_set($name, $value, $expiration_time = null) {
    $instance =& Cookies::instance();
    return $instance->setVariable($name, $value, $expiration_time);
  } // cookie_set
  
  /**
  * Shortcut to unsetVariable method of Cookies
  *
  * @param string $name Variable name
  * @return null
  */
  function cookie_unset($name) {
    $instance =& Cookies::instance();
    return $instance->unsetVariable($name);
  } // cookie_unset

?>