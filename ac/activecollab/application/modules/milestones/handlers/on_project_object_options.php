<?php

  /**
   * Milestones module on_project_object_options event handler
   *
   * @package activeCollab.modules.milestones
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
  function milestones_handle_on_project_object_options(&$options, $object, $user) {
    if(instance_of($object, 'Milestone') && $object->canEdit($user)) {
      $options->addAfter('reschedule', array(
        'url'  => $object->getRescheduleUrl(),
        'text' => lang('Reschedule'),
      ), 'edit');
    } // if
  } // milestones_handle_on_project_object_options

?>