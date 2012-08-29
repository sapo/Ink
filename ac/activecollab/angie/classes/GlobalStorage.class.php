<?php

  /**
  * Global stroage class
  *
  * Global storage provides glboal data storage accessible by usage of few 
  * simple functions. You can store any PHP data types in it and it helps you 
  * with some convinient methods for working with arrays.
  * 
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class GlobalStorage extends AngieObject {
    
    /**
    * Stored data
    *
    * @var array
    */
    var $data = array();
    
    /**
    * Return value from storage
    *
    * @param string $name
    * @return mixed
    */
    function &get($name) {
      if(isset($this->data[$name])) {
        return $this->data[$name];
      } else {
        $var = null;
        return $var;
      } // if
    } // get
    
    /**
    * Add or alter value in storage
    *
    * @param string $name
    * @param mixed $value
    * @return mixed
    */
    function &set($name, $value) {
      $this->data[$name] = $value;
      return $this->data['name'];
    } // set
    
    /**
    * Add value to an internal array
    *
    * @param string $name
    * @param mixed $value
    * @return mixed
    */
    function &arrayAppend($name, $value) {
      if(!isset($this->data[$name]) || !is_array($this->data[$name])) {
        $this->data[$name] = array();
      } // if
      
      $this->data[$name][] = $value;
      return $value;
    } // arrayAppend
    
    /**
    * Set field value of an internal array
    *
    * @param string $name
    * @param string $field
    * @param mixed $value
    * @return mixed
    */
    function &arraySetField($name, $field, $value) {
      if(!isset($this->data[$name]) || !is_array($this->data[$name])) {
        $this->data[$name] = array();
      } // if
      
      $this->data[$name][$field] = $value;
      return $this->data[$name][$field];
    } // arraySetField
    
    /**
    * Remove value from storage
    *
    * @param string $name
    * @return null
    */
    function remove($name) {
      if(isset($this->data[$name])) {
        unset($this->data[$name]);
      } // if
    } // remove
    
    /**
    * Return GS instance
    *
    * @param void
    * @return GlobalStorage
    */
    function &instance() {
      static $instance = null;
      if($instance === null) {
        $instance = new GlobalStorage();
      } // if
      return $instance;
    } // instance
    
  } // GlobalStorage
  
  /**
  * Return value from global storage
  *
  * @param string $name
  * @return mixed
  */
  function &gs_get($name) {
    static $instance = null;
    if($instance === null) {
      $instance =& GlobalStorage::instance();
    } // if
    return $instance->get($name);
  } // gs_get
  
  /**
  * Add or alter value in global storage
  *
  * @param string $name
  * @param mixed $value
  * @return mixed
  */
  function &gs_set($name, $value) {
    static $instance = null;
    if($instance === null) {
      $instance =& GlobalStorage::instance();
    } // if
    return $instance->set($name, $value);
  } // gs_set
  
  /**
  * Append value to an array variable in global storage
  *
  * @param string $name
  * @param mixed $value
  * @return mixed
  */
  function &gs_array_append($name, $value) {
    static $instance = null;
    if($instance === null) {
      $instance =& GlobalStorage::instance();
    } // if
    return $instance->arrayAppend($name, $value);
  } // gs_array_append
  
  /**
  * Set field value of an array in global storage
  *
  * @param string $name
  * @param string $field
  * @param mixed $value
  * @return mixed
  */
  function &gs_array_set_field($name, $field, $value) {
    static $instance = null;
    if($instance === null) {
      $instance =& GlobalStorage::instance();
    } // if
    return $instance->arraySetField($name, $field, $value);
  } // gs_array_set_field

?>