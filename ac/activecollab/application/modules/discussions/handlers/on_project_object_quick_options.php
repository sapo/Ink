<?php

  /**
   * Discussions module on_project_object_quick_options event handler
   *
   * @package activeCollab.modules.discussions
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
  function discussions_handle_on_project_object_quick_options(&$options, $object, $user) {
    if(instance_of($object, 'Discussion')) {
      $options->beginWith('details', array(
        'text' => lang('Toggle Details'),
        'url' => '#',
      ));
    } // if
  } // files_handle_on_project_object_quick_options

?>