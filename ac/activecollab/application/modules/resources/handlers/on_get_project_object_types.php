<?php

  /**
   * Resources module on_get_project_object_types event handler
   *
   * @package activeCollab.modules.resources
   * @subpackage handlers
   */
  
  /**
   * Return resources module project object types
   *
   * @param void
   * @return string
   */
  function resources_handle_on_get_project_object_types() {
    return 'task';
  } // resources_handle_on_get_project_object_types

?>