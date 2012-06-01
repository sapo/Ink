<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

  // log_message('error', 'fuck1!');

class Skeletor {

  var $layout;

  public function __construct(){
    $this->CI =& get_instance();
    $log_message = new document;
  }

}

class document {
  var $doctype;
  var $structure;
  var $components;
}