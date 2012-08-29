<?php

  /**
   * project_exporter_user_name helper
   * 
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */

  /**
   * Returns a link to specified object
   * 
   * - object- object for wich link will be generated
   * - url_prefix - prefix for url
   *
   * @param array $params
   * @param Smarty $smarty
   */
  function smarty_function_project_exporter_user_name($params, &$smarty) {
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User') && !instance_of($user, 'AnonymousUser') ) {
      return new InvalidParamError('object', $user, '$user is expected to be an instance of User or AnonymousUser class', true);
    } // if
    
    return clean($user->getDisplayName());
  } // smarty_function_project_exporter_user_name

?>