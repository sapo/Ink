<?php

  /**
   * Checklists handle on_milestone_add_links event
   *
   * @package activeCollab.modules.checklists
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
  function checklists_handle_on_milestone_add_links($milestone, $user, &$links) {
    if($user->getProjectPermission('checklist', $milestone->getProject()) >= PROJECT_PERMISSION_CREATE) {
      $links[lang('Checklist')] = checklists_module_add_checklist_url($milestone->getProject(), array(
        'milestone_id' => $milestone->getId(),
      ));
    } // if
  } // checklists_handle_on_milestone_add_links

?>