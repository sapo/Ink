<?php

  /**
   * Init Smarty library
   */
  
  define('SMARTY_PATH', ANGIE_PATH . '/classes/smarty');
  require_once SMARTY_PATH . '/Smarty.class.php';
  
  /**
   * Assign template variable
   *
   * @param string $varname Variable name
   * @param mixed $varvalue
   * @return boolean
   */
  function tpl_assign($varname, $varvalue = null) {
    static $instance;
    if($instance === null) {
      $instance =& Smarty::instance();
    } // if
    $instance->assign($varname, $varvalue);
  } // tpl_assign
  
  /**
   * Render template and return it as string
   *
   * @param string $resource_name
   * @param string $cache_id
   * @param string $compile_id
   * @param boolean $display
   * @return string
   */
  function tpl_fetch($resource_name, $cache_id = null, $compile_id = null, $display = false) {
    static $instance;
    if($instance === null) {
      $instance =& Smarty::instance();
    } // if
    return $instance->fetch($resource_name, $cache_id, $compile_id, $display);
  } // tpl_fetch
  
  /**
   * Render specific template
   *
   * @param string $resource_name
   * @param string $cache_id
   * @param string $compile_id
   * @return null
   */
  function tpl_display($resource_name, $cache_id = null, $compile_id = null) {
    static $instance;
    if($instance === null) {
      $instance =& Smarty::instance();
    } // if
    $instance->display($resource_name, $cache_id, $compile_id);
  } // tpl_display

?>