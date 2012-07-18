<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

  // log_message('error', 'fuck1!');

class CE_HTML_Document {

  public function new_document(){

    if ( @!is_array($_doctypes))
    {
      if (defined('ENVIRONMENT') AND is_file(APPPATH.'config/'.ENVIRONMENT.'/doctypes.php'))
      {
        include(APPPATH.'config/'.ENVIRONMENT.'/doctypes.php');
      }
      elseif (is_file(APPPATH.'config/doctypes.php'))
      {
        include(APPPATH.'config/doctypes.php');
      }
    }

    $html_doc = new HTML_Document;

    $html_doc->doctype = $_doctypes['html5'];
    $html_doc->body[] = $this->insert_element($this->body,'div',true,array('class'=>'g50'));

    return $html_doc;
  }


  public function insert_element($parent, $tag = 'div', $closed = TRUE, $attributes = array()){
    $HTML_Element = array($tag, $closed, $attributes);
    echo is_array($parent);
    // $parent = array_unshift($parent, $HTML_Element);
    // return $parent;
  }

  public function append_element ($parent){

  }



}

class HTML_Document {

  var $doctype;
  var $open_doc = "<html>\n";
  var $open_head = "<head>\n";
  var $head = array();
  var $close_head = "</head>\n";
  var $open_body = "<body>\n";
  var $body = array();
  var $close_body = "</body>\n";
  var $close_doc = "</html>";

}

// class HTML_Element 
// {

//   var $Element = array();

//   public function __construct(){
//     return $this->Element;
//   }

// }