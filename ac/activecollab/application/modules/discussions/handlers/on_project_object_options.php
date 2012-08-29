<?php

  /**
   * Discussions module on_project_object_options event handler
   *
   * @package activeCollab.modules.discussions
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
  function discussions_handle_on_project_object_options(&$options, $object, $user) {
    if(instance_of($object, 'Discussion') && $object->canEdit($user)) {
      if ($object->getIsPinned()) {
        $options->addAfter('unpin', array(
          'url'  => $object->getUnpinUrl(),
          'text' => lang('Unpin'),
          'method' => 'post',
        ), 'edit');        
      } else {
        $options->addAfter('pin', array(
          'url'  => $object->getPinUrl(),
          'text' => lang('Pin'),
          'method' => 'post',
        ), 'edit');
      } // if
    } // if
  } // milestones_handle_on_project_object_options

?>