<?php

  /**
   * Pages handle on_milestone_add_links event
   *
   * @package activeCollab.modules.pages
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
  function pages_handle_on_milestone_add_links($milestone, $user, &$links) {
    if($user->getProjectPermission('page', $milestone->getProject()) >= PROJECT_PERMISSION_CREATE) {
      $links[lang('Page')] = pages_module_add_page_url($milestone->getProject(), array(
        'milestone_id' => $milestone->getId(),
      ));
    } // if
  } // pages_handle_on_milestone_add_links

?>