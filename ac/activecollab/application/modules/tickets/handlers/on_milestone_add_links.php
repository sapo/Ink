<?php

  /**
   * Tickets handle on_milestone_add_links event
   *
   * @package activeCollab.modules.tickets
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
  function tickets_handle_on_milestone_add_links($milestone, $user, &$links) {
    if($user->getProjectPermission('ticket', $milestone->getProject()) >= PROJECT_PERMISSION_CREATE) {
      $links[lang('Ticket')] = tickets_module_add_ticket_url($milestone->getProject(), array(
        'milestone_id' => $milestone->getId(),
      ));
    } // if
  } // tickets_handle_on_milestone_add_links

?>