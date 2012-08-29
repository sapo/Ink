<?php

  /**
   * Checklists module on_get_project_object_types event handler
   *
   * @package activeCollab.modules.checklists
   * @subpackage handlers
   */
  
  /**
   * Return checklistss module project object types
   *
   * @param void
   * @return string
   */
  function checklists_handle_on_get_project_object_types() {
    return 'checklist';
  } // checklists_handle_on_get_project_object_types

?>