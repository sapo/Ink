<?php

  /**
   * Calendar on_project_tabs handler
   *
   * @package activeCollab.modules.calendar
   * @subpackage handlers
   */
  
  /**
   * Handle on project tabs event
   *
   * @param NamedList $tabs
   * @param User $logged_user
   * @param Project $project
   * @return null
   */
  function calendar_handle_on_project_tabs(&$tabs, &$logged_user, &$project) {
    $tabs->add('calendar', array(
      'text' => lang('Calendar'),
      'url' => Calendar::getProjectCalendarUrl($project),
    ));
  } // calendar_handle_on_project_tabs

?>