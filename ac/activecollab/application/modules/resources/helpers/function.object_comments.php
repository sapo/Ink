<?php

  /**
   * object_comments helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * List object comments
   * 
   * Parameters:
   * 
   * - object - Parent object. It needs to be an instance of ProjectObject class
   * - comments - List of comments. It is optional. If it is missing comments 
   *   will be loaded by calling getCommetns() method of parent object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_comments($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $logged_user = $smarty->get_template_vars('logged_user');
    if(!instance_of($logged_user, 'User')) {
      return '';
    } // if
    
    $comments = isset($params['comments']) ? $params['comments'] : $object->getComments($logged_user->getVisibility());
    if(is_foreachable($comments)) {
      foreach($comments as $comment) {
        ProjectObjectViews::log($comment, $logged_user);
      } // foreach
    } // if
    
    $count_from = 0;
    if(isset($params['count_from'])) {
      $count_from = (integer) $params['count_from'];
    } // if
    
    $smarty->assign(array(
      '_object_comments_object' => $object,
      '_object_comments_count_from' => $count_from,
      '_object_comments_comments' => $comments,
      '_object_comments_show_header' => array_var($params, 'show_header', true),
      '_object_comments_show_form' => array_var($params, 'show_form', true),
      '_object_comments_next_page' => array_var($params, 'next_page'),
    ));
    
    return $smarty->fetch(get_template_path('_object_comments', 'comments', RESOURCES_MODULE));
  } // smarty_function_object_comments

?>