<?php

  /**
   * user_link helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Display user name with a link to users profile
   * 
   * - user - User - We create link for this User
   * - short - boolean - Use short display name
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_user_link($params, &$smarty) {
    static $cache = array();
    
    $user = array_var($params, 'user');
    $short = array_var($params, 'short', false);
    
    // User instance
    if(instance_of($user, 'User')) {
      if(!isset($cache[$user->getId()])) {
        $cache[$user->getId()] = '<a href="' . $user->getViewUrl() . '" class="user_link">' . clean($user->getDisplayName($short)) . '</a>';
      } // if
      
      return $cache[$user->getId()];
      
    // AnonymousUser instance
    } elseif(instance_of($user, 'AnonymousUser') && trim($user->getName()) && is_valid_email($user->getEmail())) {
      return '<a href="mailto:' . $user->getEmail() . '" class="anonymous_user_link">' . clean($user->getName()) . '</a>';
      
    // Unknown user
    } else {
      return '<span class="unknow_user_link unknown_object_link">' . clean(lang('Unknown user')) . '</span>';
    } // if
  } // smarty_function_user_link

?>