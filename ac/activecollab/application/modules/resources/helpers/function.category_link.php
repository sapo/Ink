<?php

  /**
   * category_link helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render link to a specific category
   * 
   * Parameters:
   * 
   * - object - ProjectObject, We need to extract category data from this object
   * - getter - string, Name of the function that will return parent category ID. 
   *   By default getParentId() will be used, but it can be any method
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_category_link($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, 'Object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $getter = trim(array_var($params, 'getter', 'getParentId'));
    if($getter == '') {
      return new InvalidParamError('getter', $getter, 'Getter is expected to be valid method name', true);
    } // if
    
    $category_id = (integer) $object->$getter();
    if($category_id > 0) {
      $category = Categories::findById($category_id);
      if(instance_of($category, 'Category')) {
        return '<a href="' . $category->getViewUrl() . '">' . clean($category->getName()) . '</a>';
      } // if
    } // if
    
    return '<span class="unknown_category_link unknown_object_link">' . lang('Unknown Category') . '</span>';
  } // smarty_function_category_link

?>