<?php

	/**
   * Select Document Category helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select Document Category helper
   * 
   * Params:
   * 
   * - Standard select box attributes
   * - value - ID of selected role
   * - optional - Wether value is optional or not
   * - can_create_new - Can the user create new category or not, default is true
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_document_category($params, &$smarty) {
    static $ids = array();
    
    $user = array_var($params, 'user', null, true);
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is expected to be a valid User object', true);
    } // if
    
    $value = array_var($params, 'value', null, true);
    $can_create_new = array_var($params, 'can_create_new', true, true);
    
    $id = array_var($params, 'id', null, true);
    if(empty($id)) {
      $counter = 1;
      do {
        $id = "select_document_category_dropdown_$counter";
        $counter++;
      } while(in_array($id, $ids));
    } // if
    $ids[] = $id;
    $params['id'] = $id;
    
    $options = array();
    
    $categories = DocumentCategories::findAll($user);
    if(is_foreachable($categories)) {
      foreach($categories as $category) {
        $option_attributes = array('class' => 'object_option');
        if($value == $category->getId()) {
          $option_attributes['selected'] = true;
        } // if
        
        $options[] = option_tag($category->getName(), $category->getId(), $option_attributes);
      } // foreach
    } // if
    
    if($can_create_new) {
      $params['add_object_url'] = assemble_url('document_categories_quick_add');
      $params['object_name'] = 'document_category';
      $params['add_object_message'] = lang('Please insert new document category name');
      
      $logged_user = get_logged_user();
      if(instance_of($logged_user, 'User') && DocumentCategory::canAdd($logged_user)) {
        $options[] = option_tag('', '');
        $options[] = option_tag(lang('New Category...'), '', array('class' => 'new_object_option'));
      } // if
    } // if
    
    return select_box($options, $params) . '<script type="text/javascript">$("#' . $id . '").new_object_from_select();</script>';
  } // smarty_function_select_document_category

?>