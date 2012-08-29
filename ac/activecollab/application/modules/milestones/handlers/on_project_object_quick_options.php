<?php

  /**
   * Milestones module on_project_object_quick_options events handler
   *
   * @package activeCollab.modules.milestones
   * @subpackage handlers
   */
  
  /**
   * Populate quick object options
   *
   * @param NamedList $options
   * @param ProjectObject $object
   * @param Use $user
   * @return null
   */
  function milestones_handle_on_project_object_quick_options(&$options, $object, $user) {
    if(instance_of($object, 'Milestone') && $object->canEdit($user)) {
      $options->addAfter('reschedule', array(
        'url' => $object->getRescheduleUrl(),
        'text' => lang('Reschedule'),
      ), 'edit');
    } // if
  } // milestones_handle_on_project_object_quick_options

?>