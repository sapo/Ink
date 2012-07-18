<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

  // log_message('error', 'fuck1!');

class HTML_Document {

  private $doctype;
  private $head;
  private $body;

  private function __construct(){
    $this->doctype = new doctype;
  }

}


class doctype {

  private $doctype = 'html';

  private function __construct(){
    
  }

}