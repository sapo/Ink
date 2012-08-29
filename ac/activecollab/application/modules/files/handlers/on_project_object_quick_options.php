<?php

  /**
   * Files module on_project_object_quick_options event handler
   *
   * @package activeCollab.modules.files
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
  function files_handle_on_project_object_quick_options(&$options, $object, $user) {
    if(instance_of($object, 'File')) {
      $options->beginWith('download', array(
        'text' => lang('Download'),
        'url' => $object->getDownloadUrl(true),
      ));
      
      if($object->canEdit($user)) {
        $options->addAfter('new_revision', array(
          'text' => lang('New Version'),
          'url' => $object->getNewVersionUrl(),
        ), 'edit');
      } // if
    } // if
  } // files_handle_on_project_object_quick_options

?>