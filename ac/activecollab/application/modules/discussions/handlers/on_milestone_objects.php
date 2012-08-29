<?php

  /**
   * Discussions module on_milestone_objects event handler
   *
   * @package activeCollab.modules.discussions
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
  function discussions_handle_on_milestone_objects(&$milestone, &$objects, &$user) {
    if($user->getProjectPermission('discussion', $milestone->getProject()) >= PROJECT_PERMISSION_ACCESS) {
      $objects[lang('Discussions')] = Discussions::findByMilestone($milestone, STATE_VISIBLE, $user->getVisibility());
    } // if
  } // discussions_handle_on_milestone_objects

?>