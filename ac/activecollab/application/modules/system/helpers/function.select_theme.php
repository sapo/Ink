<?php

  /**
   * select_theme helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select theme widget
   * 
   * Parameters:
   * 
   * - optional
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_theme($params, &$smarty) {
    $themes = array();
    
    if(!defined('THEMES_PATH')) {
      define('THEMES_PATH', ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/assets/themes');
    } // if
    
    $d = dir(THEMES_PATH);
    if($d) {
      while(($entry = $d->read()) !== false) {
        if(str_starts_with($entry, '.')) {
          continue;
        } // if
        
        if(is_dir(THEMES_PATH . '/' . $entry)) {
          $themes[] = $entry;
        } // if
      } // while
      
      $value = null;
      if(array_key_exists('value', $params)) {
        $value = array_var($params, 'value');
        unset($params['value']);
      } // if
    } // if
    
    $optional = array_var($params, 'optional', false, true);
    
    $options = array();
    if($optional) {
      $options[] = option_tag(lang('-- System Default (:theme) --', array('theme' => ConfigOptions::getValue('theme'))), '');
      $options[] = option_tag('', '');
    } // if
    
    foreach($themes as $theme) {
      $option_attributes = $value == $theme ? array('selected' => true) : null;
      $options[] = option_tag($theme, $theme, $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // smarty_function_select_theme

?>