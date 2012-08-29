<?php

  /**
   * object_attachments helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * List object attachments
   * 
   * Parameters:
   * 
   * - object - selected object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_attachments($params, &$smarty) {
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    $logged_user = $smarty->get_template_vars('logged_user');
    if(!instance_of($logged_user, 'User')) {
      return '';
    } // if
    
    $attachments = $object->getAttachments();
    if(is_foreachable($attachments)) {
      foreach($attachments as $attachment) {
        ProjectObjectViews::log($attachment, $logged_user);
      } // foreach
    } // if
    
    $smarty->assign(array(
      '_object_attachments_object' => $object,
      '_object_attachments' => $attachments,
      '_object_attachments_show_header' => array_var($params, 'show_header', true),
      '_object_attachments_brief' => array_var($params, 'brief', false),
      '_object_attachments_show_empty' => array_var($params, 'show_empty', false),
    ));
    
    return $smarty->fetch(get_template_path('_object_attachments', 'attachments', RESOURCES_MODULE));
  } // object_attachments

?>