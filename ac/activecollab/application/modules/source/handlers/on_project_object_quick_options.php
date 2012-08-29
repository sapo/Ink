<?php

  /**
   * Source module on_project_object_quick_options event handler
   *
   * @package activeCollab.modules.source
   * @subpackage handlers
   * @param NamedList $options
   * @param ProjectObject $object
   * @param User $user
   * @return null
   */
  function source_handle_on_project_object_quick_options(&$options, $object, $user) {
    
    /**
     * Add a quick option which links to the list of commits related to the object
     */
    if((instance_of($object, 'Ticket') || instance_of($object, 'Discussion') || instance_of($object, 'Milestone')) && $object->canView($user)) {
      $object_commits_count = CommitProjectObjects::countByObject($object);
      
      if ($object_commits_count > 0) {
        $options->add('new_revision', array(
          'text' => lang('Commits (:object_commits)', array('object_commits' => $object_commits_count)),
          'url' => assemble_url('repository_project_object_commits', array('project_id' => $object->getProjectId(), 'object_id' => $object->getId())),
        ));
      } // if
    } // if
    
  } // source_handle_on_project_object_quick_options

?>