<?php

  /**
   * Source module on_get_project_object_types events handler
   *
   * @package activeCollab.modules.source
   * @subpackage handlers
   */
  
  /**
   * Return source module project object types
   *
   * @param void
   * @return string
   */
  function repositories_handle_on_get_project_object_types() {
    return 'repository';
  } // repositories_handle_on_get_project_object_types

?>