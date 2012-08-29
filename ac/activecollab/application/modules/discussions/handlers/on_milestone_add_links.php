<?php

  /**
   * Discussions handle on_milestone_add_links event
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Handle on_milestone_add_links event
   *
   * @param Milestone $milestone
   * @param User $user
   * @param array $links
   * @return null
   */
  function discussions_handle_on_milestone_add_links($milestone, $user, &$links) {
    if($user->getProjectPermission('discussion', $milestone->getProject()) >= PROJECT_PERMISSION_CREATE) {
      $links[lang('Discussion')] = discussions_module_add_discussion_url($milestone->getProject(), array(
        'milestone_id' => $milestone->getId(),
      ));
    } // if
  } // discussions_handle_on_milestone_add_links

?>