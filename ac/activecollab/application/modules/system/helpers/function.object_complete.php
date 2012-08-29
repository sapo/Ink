<?php

  /**
   * object_complete helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render complete / open widget for an object
   * 
   * Params:
   * 
   * - object - object that needs to be competed
   * - user - user who is viewing the page
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_complete($params, &$smarty) {
    static $ids = array();
    
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is not valid instance of ProjectObject class', true);
    } // if
    
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is expected to be an instance of User class', true);
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      do {
        $id = 'object_complete_reopen_' . make_string(40);
      } while(in_array($id, $ids));
    } // if
    
    $ids[] = $id;
    
    if($object->can_be_completed && $object->canChangeCompleteStatus($user)) {
      $href = instance_of($object, 'Task') ? $object->getOpenUrl(true, true) : $object->getOpenUrl(true);
      
      if($object->getCompletedOn()) {
        $params = array(
          'id'    => $id,
          'href'  => $href,
          'title' => lang('Reopen task'),
          'class' => 'reopen_task'
        );
        
        $result = open_html_tag('a', $params) . '<img src="' . get_image_url('icons/checked.gif') . '" alt="" /></a>';
      }  else {
        $href = instance_of($object, 'Task') ? $object->getCompleteUrl(true, true) : $object->getCompleteUrl(true);
        
        $params = array(
          'id'    => $id,
          'href'  => $href,
          'title' => lang('Complete task'),
          'class' => 'complete_task',
        );
        
        $result = open_html_tag('a', $params) . '<img src="' . get_image_url('icons/not-checked.gif') . '" alt="" /></a>';
      } // if
      
      return $result . "\n<script type=\"text/javascript\">App.layout.init_complete_open_link('" . $id . "')</script>";
    } else {
      return '';
    } // if
  } // smarty_function_object_complete

?>