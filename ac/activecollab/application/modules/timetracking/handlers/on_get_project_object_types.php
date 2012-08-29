<?php

  /**
   * Timetracking module on_get_project_object_types event handler
   *
   * @package activeCollab.modules.timetracking
   * @subpackage handlers
   */
  
  /**
   * Return timetracking module project object types
   *
   * @param void
   * @return string
   */
  function timetracking_handle_on_get_project_object_types() {
    return 'timerecord';
  } // timetracking_handle_on_get_project_object_types

?>