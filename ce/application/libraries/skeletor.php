<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

  // log_message('error', 'fuck1!');

class Skeletor {

  var $structure;

  public function __construct(){
    $this->CI =& get_instance();
    $structure = new StdClass();
  }

}