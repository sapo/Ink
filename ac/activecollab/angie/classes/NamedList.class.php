<?php

  /**
   * Name list
   */
  class NamedList extends AngieObject {
    
    /**
     * List data
     * 
     * @var array
     */
    var $data = array();
    
    /**
     * Add data to the list
     *
     * @param string $name
     * @param mixed $data
     * @return mixed
     */
    function add($name, $data) {
      $this->data[$name] = $data;
      return $data;
    } // add
    
    
    /**
     * Remove data from the list
     *
     * @param string $name
     * @return void
     */
    function remove($name) {
      if (isset($this->data[$name])) {
        unset($this->data[$name]);
      }
    } // remove
    
    /**
     * Add data to the beginning of the list
     *
     * @param string $name
     * @param mixed $data
     * @return mixed
     */
    function beginWith($name, $data) {
      $new_data = array($name => $data);
      
      foreach($this->data as $k => $v) {
        $new_data[$k] = $v;
      } // foreach
      
      $this->data = $new_data;
      
      return $data;
    } // beginWith
    
    /**
     * Add data before $before element
     *
     * @param string $name
     * @param mixed $data
     * @param string $before
     * @return mixed
     */
    function addBefore($name, $data, $before) {
      $new_data = array();
      $added = false;
      
      foreach($this->data as $k => $v) {
        if($k == $before) {
          $new_data[$name] = $data;
          $added = true;
        } // if
        
        $new_data[$k] = $v;
      } // foreach
      
      if(!$added) {
        $new_data[$name] = $data;
      } // if
      
      $this->data = $new_data;
      
      return $data;
    } // addBefore
    
    /**
     * Add item after $after list element
     *
     * @param string $name
     * @param mixed $data
     * @param string $after
     * @return mixed
     */
    function addAfter($name, $data, $after) {
      $new_data = array();
      $added = false;
      
      foreach($this->data as $k => $v) {
        $new_data[$k] = $v;
        
        if($k == $after) {
          $new_data[$name] = $data;
          $added = true;
        } // if
      } // foreach
      
      if(!$added) {
        $new_data[$name] = $data;
      } // if
      
      $this->data = $new_data;
      
      return $data;
    } // addAfter
    
    /**
     * Return number of items in the list
     *
     * @param void
     * @return integer
     */
    function count() {
      return count($this->data);
    } // count
    
  } // NamedList

?>