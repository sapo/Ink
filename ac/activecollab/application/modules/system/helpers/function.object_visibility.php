<?php

  /**
   * Visibility helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Show object visibility if it's private
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_visibility($params, &$smarty) {
  	static $ids = array();
    
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is not valid instance of ProjectObject class', true);
    } // if
    
    if($object->getVisibility() > VISIBILITY_PRIVATE) {
      return '';
    } // if
    
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is expected to be an instance of User class', true);
    } // if
    
    if(!$user->canSeePrivate()) {
      return '';
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      do {
        $id = 'object_visibility_' . make_string(40);
      } while(in_array($id, $ids));
    } // if
    
    $ids[] = $id;
    
    return open_html_tag('a', array(
      'href'  => assemble_url('project_object_visibility', array('project_id' => $object->getProjectId(), 'object_id' => $object->getId())),
      'title' => lang('Private :type', array('type' => Inflector::humanize($object->getType()))),
      'class' => 'object_visibility',
      'id'    => $id,
    )) . '<img src="' . get_image_url('private.gif') . '" alt="" /></a><script type="text/javascript">App.widgets.ObjectVisibility.init("' . $id . '");</script>';
  } // smarty_function_object_visibility

?>