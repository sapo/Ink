<?php

  /**
   * Tickets module on_milestone_objects event handler
   *
   * @package activeCollab.modules.tickets
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
  function tickets_handle_on_milestone_objects(&$milestone, &$objects, &$user) {
    if($user->getProjectPermission('ticket', $milestone->getProject()) >= PROJECT_PERMISSION_ACCESS) {
      $objects[lang('Tickets')] = Tickets::findByMilestone($milestone, STATE_VISIBLE, $user->getVisibility());
    } // if
  } // tickets_handle_on_milestone_objects

?>