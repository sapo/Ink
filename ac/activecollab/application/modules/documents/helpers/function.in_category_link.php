<?php

  /**
   * in_category_link helper
   *
   * @package activeCollab.modules.documents
   * @subpackage helpers
   */
  
  /**
   * Render link to a specific category
   * 
   * Parameters:
   * 
   * - category_id - integer, return parent category ID. 
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_in_category_link($params, &$smarty) {    
    $category_id = array_var($params, 'category_id');
    $user = array_var($params, 'user', null);
    
    $category = DocumentCategories::findById($category_id);
    if(instance_of($category, 'DocumentCategory')) {
    	if($category->canView($user)) {
      	return '<a href="' . $category->getViewUrl() . '">' . clean($category->getName()) . '</a>';
    	} else {
    		return clean($category->getName());
    	} // if
    } // if
    return '<span class="unknown_category_link unknown_object_link">' . lang('Unknown Category') . '</span>';
  } // smarty_function_in_category_link

?>