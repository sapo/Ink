<?php

  /**
  * File does not exists exception
  *
  * @version 1.0
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class FileDnxError extends Error {
  
    /**
    * Path of the requested file
    *
    * @var string
    */
    var $file_path;
    
    /**
    * Construct the FileDnxError
    *
    * @param string $file_path
    * @param string $message
    * @param boolean $is_fatal
    * @return FileDnxError
    */
    function __construct($file_path, $message = null, $is_fatal = false) {
      if(is_null($message)) {
        $message = "File '$file_path' doesn't exists";
      } // if
      
      $this->setFilePath($file_path);
      $this->is_fatal = (boolean) $is_fatal;
      parent::__construct($message);
    } // __construct
    
    /**
    * Return errors specific params...
    *
    * @param void
    * @return array
    */
    function getAdditionalParams() {
      return array(
        'file path' => $this->getFilePath()
      ); // array
    } // getAdditionalParams
    
    // -------------------------------------------------------
    // Getters and setters
    // -------------------------------------------------------
    
    /**
    * Get file_path
    *
    * @param null
    * @return string
    */
    function getFilePath() {
      return $this->file_path;
    } // getFilePath
    
    /**
    * Set file_path value
    *
    * @param string $value
    * @return null
    */
    function setFilePath($value) {
      $this->file_path = $value;
    } // setFilePath
  
  } // FileDnxError

?>