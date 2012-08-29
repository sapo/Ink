<?php

  /**
   * mobile_access_action_by helper
   *
   * @package activeCollab.modules.mobile_access
   * @subpackage helpers
   */
  
  /**
   * Render action name and user link based on input parameters
   * 
   * - action - Action string, default is 'Posted'. It is used for lang retrival
   * - user - User who took the action. Can be registered user or anonymous user
   * - short_names - Use short user name
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_mobile_access_action_by($params, &$smarty) {
    $action = clean(array_var($params, 'action', 'Posted'));
    
    $short_names = (boolean) array_var($params, 'short_names');
    
    $user = array_var($params, 'user');
    if(instance_of($user, 'User')) {
      return "<span class='no_break'>".lang($action)."</span> ".lang('by')." <a href='mailto:".mobile_access_module_get_view_url($user)."'>".$user->getDisplayName($short_names)."</a>";
    } elseif(instance_of($user, 'AnonymousUser')) {
      return "<span class='no_break'>".lang($action)."</span> ".lang('by')." <a href='mailto:".$user->getEmail()."'>".$user->getName()."</a>";
    } else {
      return new InvalidParamError('user', $user, '$user is required attribute and it needs to be instance of User or AnonymousUser class', true);
    } // if
  } // smarty_function_mobile_access_action_by

?>