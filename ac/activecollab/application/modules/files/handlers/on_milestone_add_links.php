<?php

  /**
   * Files handle on_milestone_add_links event
   *
   * @package activeCollab.modules.files
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
  function files_handle_on_milestone_add_links($milestone, $user, &$links) {
    if($user->getProjectPermission('file', $milestone->getProject()) >= PROJECT_PERMISSION_CREATE) {
      $links[lang('File')] = files_module_upload_url($milestone->getProject(), array(
        'milestone_id' => $milestone->getId(),
      ));
    } // if
  } // files_handle_on_milestone_add_links

?>