<?php

  /**
   * Tickets module on_get_completable_project_object_types events handler
   *
   * @package activeCollab.modules.tickets
   * @subpackage handlers
   */
  
  /**
   * Return completable tickets module types
   *
   * @param void
   * @return string
   */
  function tickets_handle_on_get_completable_project_object_types() {
    return 'ticket';
  } // tickets_handle_on_get_completable_project_object_types

?>