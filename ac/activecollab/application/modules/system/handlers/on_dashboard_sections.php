<?php

  /**
   * System on_dashboard_sections handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle on_dashboard_sections event
   *
   * @param NamedList $sections
   * @param User $user
   * @return null
   */
  function system_handle_on_dashboard_sections(&$sections, &$user) {
    $sections->add('recent_activities', array(
      'text' => lang('Recent Activities'),
      'url' => assemble_url('recent_activities'),
    ));
    
    $sections->add('active_projects', array(
      'text' => lang('Active Projects'),
      'url' => assemble_url('active_projects'),
    ));
    
    $count_new = ProjectObjects::countNew($user);
    if ($count_new > 0) {
      $sections->add('new_updated', array(
        'text' => lang('New / Updated <span class="slip">:count</span>', array('count' => $count_new)),
        'url' => assemble_url('new_since_last_visit'),
      ));
    } // if
    
    $count_late_today = ProjectObjects::countLateAndToday($user, null, get_completable_project_object_types());
    if ($count_late_today > 0) {
      $sections->add('late_today', array(
        'text' => lang('Late / Today <span class="slip">:count</span>', array('count' => $count_late_today)),
        'url' => assemble_url('late_today'),
      ));
    } // if
  } // system_handle_on_dashboard_sections

?>