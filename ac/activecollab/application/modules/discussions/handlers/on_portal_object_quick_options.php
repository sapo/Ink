<?php

  /**
   * Discussions module on_portal_object_quick_options event handler
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */
  
  /**
   * Populate quick portal object options
   *
   * @param NamedList $options
   * @param ProjectObject $object
   * @param Portal $portal
   * @param Commit $commit
   * @param string $file
   * @return null
   */
  function discussions_handle_on_portal_object_quick_options(&$options, $object, $portal = null, $commit = null, $file = null) {
    if(instance_of($object, 'Discussion')) {
      $options->beginWith('details', array(
        'text' => lang('Toggle Details'),
        'url' => '#',
      ));
    } // if
  } // files_handle_on_portal_object_quick_options

?>