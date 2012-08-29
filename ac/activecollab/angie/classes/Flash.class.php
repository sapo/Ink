<?php

  /**
  * Flash service
  *
  * Purpose of this service is to make some data available across pages. Flash
  * data is available on the next page but deleted when execution reach its end.
  *
  * Usual use of Flash is to make possible that current page pass some data
  * to the next one (for instance success or error message before HTTP redirect).
  *
  * Flash service as a concep is taken from Rails.
  *
  * @author Ilija Studen <ilija.studen@gmial.com>
  */
  class Flash extends AngieObject {
  
    /**
    * Data that prevous page left in the Flash
    *
    * @var array
    */
    var $previous = array();
    
    /**
    * Data that current page is saving for the next page
    *
    * @var array
    */
    var $next = array();
    
    /**
    * Init Flash service
    *
    * @param void
    * @return null
    */
    function init() {
      if(isset($this) && instance_of($this, 'Flash')) {
        $this->readFlash();
      } else {
        $instance =& Flash::instance();
        $instance->init();
      } // if
    } // init
    
    /**
    * Return specific variable from the flash. If value is not found NULL is
    * returned
    *
    * @param string $var Variable name
    * @return mixed
    */
    function getVariable($var) {
      return isset($this->previous[trim($var)]) ? $this->previous[trim($var)] : null;
    } // end func getVariable
    
    /**
    * Add specific variable to the flash. This variable will be available on the
    * next page unlease removed with the removeVariable() or clear() method
    *
    * @param string $var Variable name
    * @param mixed $value Variable value
    * @return void
    */
    function addVariable($var, $value) {
      $this->next[trim($var)] = $value;
      $this->writeFlash();
    } // end func addVariable
    
    /**
    * Remove specific variable for the Flash
    *
    * @param string $var Name of the variable that need to be removed
    * @return void
    */
    function removeVariable($var) {
      if(isset($this->next[trim($var)])) {
        unset($this->next[trim($var)]);
      } // if
      $this->writeFlash();
    } // end func removeVariable
    
    /**
    * Call this function to clear flash. Note that data that previous page
    * stored will not be deleted - just the data that this page saved for
    * the next page
    *
    * @param void
    * @return void
    */
    function clear() {
      $this->next = array();
    } // end func cleare
    
    /**
    * This function will read flash data from the $_SESSION variable
    * and load it into $this->previous array
    *
    * @param void
    * @return void
    */
    function readFlash() {
      $flash_data = array_var($_SESSION, 'flash_data');
      
      // If we have flash data set it to previous array and forget it :)
      if(!is_null($flash_data)) {
        if(is_array($flash_data)) $this->previous = $flash_data;
        unset($_SESSION['flash_data']);
      } // if
    } // end func readFlash
    
    /**
    * Save content of the $this->next array into the $_SESSION autoglobal var
    *
    * @param void
    * @return void
    */
    function writeFlash() {
      $_SESSION['flash_data'] = $this->next;
    } // end func writeFlash
    
    /**
    * Return flash service instance
    *
    * @param void
    * @return FlashService
    */
    function &instance() {
      static $instance;
      if(!instance_of($instance, 'Flash')) {
        $instance = new Flash();
      } // if
      return $instance;
    } // end func instance
    
  } // end class Flash
  
  // ================================================================
  //  Shortcut methods
  // ================================================================
  
  /**
  * Interface to flash add method
  *
  * @param string $name Variable name
  * @param mixed $value Value that need to be set
  * @return null
  */
  function flash_add($name, $value) {
    $flash =& Flash::instance();
    $flash->addVariable($name, $value);
  } // flash_add
  
  /**
  * Shortcut method for adding success var to flash
  *
  * @param string $message Success message
  * @param array $replacements
  * @param boolean $not_lang
  * @return null
  */
  function flash_success($message, $replacements = null, $not_lang = false) {
    $set = $not_lang ? $message : lang($message, $replacements);
    flash_add('success', $set);
  } // flash_success
  
  /**
  * Shortcut method for adding error var to flash
  *
  * @param string $message Error message
  * @param array $replacements
  * @param boolean $not_lang
  * @return null
  */
  function flash_error($message, $replacements = null, $not_lang = false) {
    $set = $not_lang ? $message : lang($message, $replacements);
    flash_add('error', $set);
  } // flash_error
  
  /**
  * Return variable from flash. If variable DNX NULL is returned
  *
  * @param string $name Variable name
  * @return mixed
  */
  function flash_get($name) {
    $flash =& Flash::instance();
    return $flash->getVariable($name);
  } // flash_get

?>