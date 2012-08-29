<?php

  /**
   * select_user helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select user control
   * 
   * Supported paramteres:
   * 
   * - all HTML attributes
   * - value - ID of selected user
   * - project - Instance of selected project, if NULL all users will be listed
   * - optional - Is this value optional
   * - optional_caption - Value used as text for optional select item
   * - users - id-s of preloaded users
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_user($params, &$smarty) {
    $preloaded_ids = array_var($params, 'users',null);
    if($preloaded_ids) {
      $users = Users::findForSelectByIds($preloaded_ids);
    } else {
      $users = Users::findForSelect(array_var($params, 'company'), array_var($params, 'project'));
    } // if
    
    if(array_key_exists('company', $params)) {
      unset($params['company']);
    } // if
    
    if(array_key_exists('project', $params)) {
      unset($params['project']);
    } // if

    $value = array_var($params, 'value', null, true);
    $optional = array_var($params, 'optional', false, true);
    
    $options = array();
    if($optional) {
      $options[] = option_tag(lang(array_var($params, 'optional_caption', '-- Select user --')), '');
    } // if
    
    if(is_foreachable($users)) {
      foreach($users as $company_name => $company_users) {
        $company_options = array();
        foreach($company_users as $user) {
          $option_attributes = $user['id'] == $value ? array('selected' => true) : null;
          $company_options[] = option_tag($user['display_name'], $user['id'], $option_attributes);
        } // foreach
        
        $options[] = option_group_tag($company_name, $company_options);
      } // if
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_user

?>