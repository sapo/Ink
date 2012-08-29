<?php

  /**
   * Source module on_project_object_options event handler
   *
   * @package activeCollab.modules.source
   * @subpackage handlers
   */
  
  /**
   * Populate object options array
   *
   * @param NamedList $options
   * @param ProjectObject $object
   * @param User $user
   * @return null
   */
  function source_handle_on_project_object_options(&$options, $object, $user) {
    // Trash exposes all commits for individual removal/restoration, which must not be allowed
    if (instance_of($object, 'Repository') || instance_of($object, 'Commit')) {
      $options->remove('move_to_trash');
    } // if
    
    if (instance_of($object, 'Repository') && $object->canEdit($user)) {
      $options->add('repository_users', array(
        'text' => lang('Manage Repository Users'),
        'url' => assemble_url('repository_users', array('repository_id'=>$object->getId(), 'project_id' => $object->getProjectId()))
      ));
      
      $options->add('repository_delete', array(
        'text' => lang('Delete repository'),
        'url' => assemble_url('repository_delete', array('repository_id'=>$object->getId(), 'project_id' => $object->getProjectId()), array('id' => 'repository_delete'))
      ));
    } // if
  } // source_handle_on_project_object_options

?>