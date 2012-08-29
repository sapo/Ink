<?php

  /**
   * mobile_access_add_comment_form
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */

  /**
   * Render add comment form
   * 
   * Parameters:
   * 
   * - parent - comment parent
   * - comment_data - POST comment data
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_mobile_access_add_comment_form($params, &$smarty) {
    $parent = array_var($params, 'parent');
    if(!instance_of($parent, 'ProjectObject')) {
      return new InvalidParamError('object', $parent, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $smarty->assign(array(
      '_mobile_access_add_comment_form_add_comment_url' => mobile_access_module_get_add_comment_url($parent),
      '_mobile_access_add_comment_form_comment_data'  => array_var($params, 'comment_data', array()),
    ));
    
    return $smarty->fetch(get_template_path('_add_comment_form', null, MOBILE_ACCESS_MODULE));
  } // smarty_function_mobile_access_display_filter_list

?>