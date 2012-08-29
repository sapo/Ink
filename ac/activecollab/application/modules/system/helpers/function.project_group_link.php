<?php

  /**
   * project_group_link helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render project group link
   * 
   * Parameters:
   * 
   * - group - ProjectGroup
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_group_link($params, &$smarty) {
    $group = array_var($params, 'group');
    if(!instance_of($group, 'ProjectGroup')) {
      return new InvalidParamError('group', $group, '$group is expected to be an instance of ProjectGroup class', true);
    } // if
    
    unset($params['group']);
    $params['href'] = $group->getViewUrl();
    
    return open_html_tag('a', $params) . clean($group->getName()) . '</a>';
  } // smarty_function_project_group_link

?>