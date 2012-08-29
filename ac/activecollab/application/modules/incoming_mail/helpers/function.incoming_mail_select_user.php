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
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_incoming_mail_select_user($params, &$smarty) {
    $project = array_var($params, 'project');
    if (!instance_of($project, 'Project')) {
      return new InvalidParamError('object', $project, '$project is expected to be an instance of Project class', true);
    } // if
    
    $users = Users::findForSelect(null, array_var($params, 'project'));
    $value = array_var($params, 'value', null, true);
    
    $options = array(
      option_tag(lang('Original Author'), 'original_author', $value == 'original_author' ? array('selected' => true) : null)
    );
    
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
  } // smarty_function_incoming_mail_select_user

?>