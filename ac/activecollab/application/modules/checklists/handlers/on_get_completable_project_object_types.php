<?php

  /**
   * Checklists module on_get_completable_project_object_types events handler
   *
   * @package activeCollab.modules.checklists
   * @subpackage handlers
   */
  
  /**
   * Return completable checklist module types
   *
   * @param void
   * @return string
   */
  function checklists_handle_on_get_completable_project_object_types() {
    return 'checklist';
  } // checklists_handle_on_get_completable_project_object_types

?>