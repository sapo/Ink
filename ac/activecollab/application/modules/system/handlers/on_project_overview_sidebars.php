<?php

  /**
   * System module on_project_overview_sidebars event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Add sidebars to project overview page
   *
   * @param array $sidebars
   * @param Project $project
   * @param User $user
   * @return null
   */
  function system_handle_on_project_overview_sidebars(&$sidebars, &$project, &$user) {

    // only project leader, system administrators and project manages can see last activity
    $can_see_last_activity = $user->isProjectLeader($project) || $user->isAdministrator() || $user->isProjectManager();

    $project_users = $project->getUsers();
    if (is_foreachable($project_users)) {
      require_once(SYSTEM_MODULE_PATH.'/helpers/function.user_link.php');
      require_once(SMARTY_PATH.'/plugins/modifier.ago.php');
      
      $output = '';
      $sorted_users = Users::groupByCompany($project_users);
      foreach ($sorted_users as $sorted_user) {
        $company = $sorted_user['company'];
        $users = $sorted_user['users'];
        if (is_foreachable($users)) {
          $output.= '<h3><a href="' . $company->getViewUrl() . '">' . clean($company->getName()) . '</a></h3>';
          $output.= '<ul class="company_users">';
          foreach ($users as $current_user) {
            $last_seen = '';
            if ($can_see_last_activity && ($user->getId() != $current_user->getId())) {
              $last_seen = smarty_modifier_ago($current_user->getLastActivityOn());
            } // if
          	$output.= '<li><span class="icon_holder"><img src="'. $current_user->getAvatarUrl() . '" /></span> '.smarty_function_user_link(array('user' => $current_user)).' ' . $last_seen. '</li>';
          } // foreach
          $output.= '</ul>';
        } // if
      } // foreach
        
      $sidebars[] = array(
        'label'         => lang('People on This Project'),
        'is_important'  => false,
        'id'            => 'project_people',
        'body'          => $output,
      );
    } // if
  } // system_handle_on_project_overview_sidebars

?>


