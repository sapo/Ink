<?php

  /**
   * Pages module on_milestone_objects event handler
   *
   * @package activeCollab.modules.pages
   * @subpackage handlers
   */

  /**
   * Populate $objects with object that $user can see
   *
   * @param Milestone $milestone
   * @param array $objects
   * @param User $user
   * @return null
   */
  function pages_handle_on_milestone_objects(&$milestone, &$objects, &$user) {
    if($user->getProjectPermission('page', $milestone->getProject()) >= PROJECT_PERMISSION_ACCESS) {
      $objects[lang('Pages')] = Pages::findByMilestone($milestone, STATE_VISIBLE, $user->getVisibility());
    } // if
  } // pages_handle_on_milestone_objects

?>