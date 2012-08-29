<?php

  /**
   * Checklists module on_milestone_objects event handler
   *
   * @package activeCollab.modules.tickets
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
  function checklists_handle_on_milestone_objects(&$milestone, &$objects, &$user) {
    if($user->getProjectPermission('checklist', $milestone->getProject()) >= PROJECT_PERMISSION_ACCESS) {
      $objects[lang('Checklists')] = Checklists::findByMilestone($milestone, STATE_VISIBLE, $user->getVisibility());
    } // if
  } // checklists_handle_on_milestone_objects

?>