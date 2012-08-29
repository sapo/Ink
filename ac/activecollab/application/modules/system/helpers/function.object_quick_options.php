<?php

  /**
   * object_quick_options helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render quick options section for a given object
   * 
   * Parameters:
   * 
   * - object - An instance of ProjectObject, User or Company class
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_quick_options($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject') && !instance_of($object, 'Company') && !instance_of($object, 'User')) {
      return new InvalidParamError('object', $object, '$object is not valid instance of ProjectObject, Company or User class', true);
    } // if
    
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is expected to be an instance of User class', true);
    } // if
    
    $options = $object->getQuickOptions($user);
    if(instance_of($options, 'NamedList') && is_foreachable($options->data)) {
      $smarty->assign('_quick_options', $options);
      return $smarty->fetch(get_template_path('_object_quick_options', null, SYSTEM_MODULE));
    } else {
      return '';
    } // if
  } // smarty_function_object_quick_options

?>