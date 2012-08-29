<?php

  /**
   * Pages module on_project_object_quick_options events handler
   *
   * @package activeCollab.modules.pages
   * @subpackage handlers
   */
  
  /**
   * Populate quick object options
   *
   * @param NamedList $options
   * @param Page $object
   * @param Use $user
   * @return null
   */
  function pages_handle_on_project_object_quick_options(&$options, $object, $user) {
    if(instance_of($object, 'Page') && $object->canEdit($user)) {
      if($object->getIsArchived()) {
        $options->addAfter('unarchive', array(
          'url'  => $object->getUnarchiveUrl(),
          'text' => lang('Unarchive'),
          'method' => 'post',
          'confirm' => lang('Are you sure that you want to mark this archived page as active?'),
        ), 'edit');
      } else {
        $options->addAfter('archive', array(
          'url'  => $object->getArchiveUrl(),
          'text' => lang('Archive'),
          'method' => 'post',
          'confirm' => lang('Are you sure that you want to archive this page?'),
        ), 'edit');
      } // if
    } // if
  } // pages_handle_on_project_object_quick_options

?>