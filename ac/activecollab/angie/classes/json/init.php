<?php

  /**
   * Init JSON library
   * 
   * JSON library introduces support for JSON in Angie. JSON (JavaScript Object 
   * Notation) is a lightweight data-interchange format. It is easy for humans to 
   * read and write. It is easy for machines to parse and generate. It is based 
   * on a subset of the JavaScript Programming Language, Standard ECMA-262 3rd 
   * Edition - December 1999. This feature can also be found in  Python. JSON is 
   * a text format that is completely language independent but uses conventions 
   * that are familiar to programmers of the C-family of languages, including C, 
   * C++, C#, Java, JavaScript, Perl, TCL, and many others. These properties make 
   * JSON an deal data-interchange language.
   */
  
  define('JSON_LIBRARY_PATH', ANGIE_PATH . '/classes/json');
  require JSON_LIBRARY_PATH . '/JSON.class.php';
  
  /**
   * Encode $value to JSON
   *
   * @param string $value
   * @return string
   */
  function do_json_encode($value) {
    static $json;
    
    if(empty($json)) {
      $json =& Services_JSON::instance();
    } // if
    
    if(instance_of($value, 'DateValue')) {
      return $json->encode($value->toMySQL());
    } // if
    
    return $json->encode($value);
  } // do_json_encode
  
  /**
   * Decode JSON string
   *
   * @param string $json
   * @return string
   */
  function do_json_decode($json) {
    static $json;
    
    if(empty($json)) {
      $json =& Services_JSON::instance();
    } // if
    
    return $json->decode($json);
  } // do_json_decode

?>