<?php

  /**
   * Files module on_milestone_objects event handler
   *
   * @package activeCollab.modules.files
   * @subpackage handlers
   */

  /**
   * Populate $objects with objects that $user can see
   *
   * @param Milestone $milestone
   * @param array $objects
   * @param User $user
   * @return null
   */
  function files_handle_on_milestone_objects(&$milestone, &$objects, &$user) {
    if($user->getProjectPermission('file', $milestone->getProject()) >= PROJECT_PERMISSION_ACCESS) {
      $objects[lang('Files')] = Files::findByMilestone($milestone, STATE_VISIBLE, $user->getVisibility());
    } // if
  } // files_handle_on_milestone_objects

?>