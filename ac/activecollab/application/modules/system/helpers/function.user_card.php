<?php

  /**
   * user_card helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Show user card
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_user_card($params, &$smarty) {
    $user = array_var($params, 'user');
    if(!instance_of($user, 'User')) {
      return new InvalidParamError('user', $user, '$user is expected to be an instance of User class', true);
    } // if
    
    $smarty->assign(array(
      '_card_user' => $user,
      '_card_options' => $user->getOptions(get_logged_user()),
    ));
    return $smarty->fetch(get_template_path('_card', 'users', SYSTEM_MODULE));
  } // smarty_function_user_card

?>