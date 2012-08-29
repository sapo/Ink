<?php

  /**
   * select_company helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select company box
   * 
   * Parameters:
   * 
   * - value - Value of selected company
   * - optional - Is value of this field optional or not
   * - exclude - Array of company ID-s that will be excluded
   * - can_create_new - Should this select box offer option to create a new 
   *   company from within the list
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_company($params, &$smarty) {
    static $ids = array();
    
    $companies = Companies::getIdNameMap(array_var($params, 'companies'));
    
    $value = array_var($params, 'value', null, true);
    $id = array_var($params, 'id', null, true);
    if(empty($id)) {
      $counter = 1;
      do {
        $id = "select_company_dropdown_$counter";
        $counter++;
      } while(in_array($id, $ids));
    } // if
    $ids[] = $id;
    $params['id'] = $id;
    
    $optional = array_var($params, 'optional', false, true);
    $exclude = array_var($params, 'exclude', array(), true);
    if(!is_array($exclude)) {
      $exclude = array();
    } // if
    $can_create_new = array_var($params, 'can_create_new', true, true);
    
    if($optional) {
      $options = array(option_tag(lang('-- None --'), ''), option_tag('', ''));
    } else {
      $options = array();
    } // if
    
    foreach($companies as $company_id => $company_name) {
      if(in_array($company_id, $exclude)) {
        continue;
      } // if
      
      $option_attributes = array('class' => 'object_option');
      if($value == $company_id) {
        $option_attributes['selected'] = true;
      } // if
      
      $options[] = option_tag($company_name, $company_id, $option_attributes);
    } // if
    
    if($can_create_new) {
      $logged_user = get_logged_user();
      if(instance_of($logged_user, 'User') && Company::canAdd($logged_user)) {
        $params['add_object_url'] = assemble_url('people_companies_quick_add');
        $params['object_name'] = 'company';
        $params['add_object_message'] = lang('Please insert new company name');
        
        $options[] = option_tag('', '');
        $options[] = option_tag(lang('New Company...'), '', array('class' => 'new_object_option'));
      } // if
    } // if
    
    return select_box($options, $params) . '<script type="text/javascript">$("#' . $id . '").new_object_from_select();</script>';
  } // smarty_function_select_company

?>