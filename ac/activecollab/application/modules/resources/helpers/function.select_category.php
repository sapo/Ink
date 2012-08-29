<?php

  /**
   * select_category helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render select category control
   * 
   * Supported paramteres:
   * 
   * - all HTML attributes
   * - project - Parent project, required
   * - module - Module
   * - controller - Controller name
   * - value - ID of selected category
   * - optional - If false there will be no -- none -- option
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_category($params, &$smarty) {
    static $ids = array();
    
    $project = array_var($params, 'project', null, true);
    if(!instance_of($project, 'Project')) {
      return new InvalidParamError('project', $project, 'Project parameter is required for select category helper and it needs to be an instance of Project class', true);
    } // if
    
    $user = array_var($params, 'user', null, true);
    
    $module = trim(array_var($params, 'module', null, true));
    if($module == '') {
      return new InvalidParamError('module', $module, 'Module parameter is required for select category helper', true);
    } // if
    
    $controller = trim(array_var($params, 'controller', null, true));
    if($controller == '') {
      return new InvalidParamError('controller', $controller, 'Controller parameter is required for select category helper', true);
    } // if
    
    $id = array_var($params, 'id', null, true);
    if(empty($id)) {
      $counter = 1;
      do {
        $id = "select_category_$counter";
        $counter++;
      } while(in_array($id, $ids));
    } // if
    $params['id'] = $id;
    
    $value = array_var($params, 'value', null, true);
    $optional = array_var($params, 'optional', true, true);
    
    $options = array();
    if($optional) {
      $options[] = option_tag(lang('-- None --'), '');
    } // if
    
    $categories = Categories::findByModuleSection($project, $module, $controller);
    if(is_foreachable($categories)) {
      foreach($categories as $category) {
        $option_attributes = array('class' => 'object_option');
        if($category->getId() == $value) {
          $option_attributes['selected'] = true;
        } // if
        
        $options[] = option_tag($category->getName(), $category->getId(), $option_attributes);
      } // foreach
    } // if
    
    if(instance_of($user, 'User') && Category::canAdd($user, $project)) {
      $params['add_object_url'] = Category::getQuickAddUrl($project, $controller, $module);
      $params['object_name'] = 'category';
      $params['add_object_message'] = lang('Please insert new category name');
      
      $options[] = option_tag('', '');
      $options[] = option_tag(lang('New Category...'), '', array('class' => 'new_object_option'));
    } // if
    
    return select_box($options, $params) . '<script type="text/javascript">$("#' . $id . '").new_object_from_select();</script>';
  } // smarty_function_select_category

?>