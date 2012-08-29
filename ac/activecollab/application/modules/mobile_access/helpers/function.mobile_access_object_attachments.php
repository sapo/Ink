<?php

  /**
   * mobile_access_object_attachments
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */

  /**
   * Render object attachments
   * 
   * Parameters:
   * 
   * - object - object which has attachments
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_object_attachments($params, &$smarty) {
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
      '_mobile_access_object_attachments_object' => $object,
      '_mobile_access_object_attachments' => $attachments,
    ));
    
    return $smarty->fetch(get_template_path('_object_attachments', null, MOBILE_ACCESS_MODULE));
  } // smarty_function_mobile_access_object_comments

?>