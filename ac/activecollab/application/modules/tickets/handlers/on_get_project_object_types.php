<?php

  /**
   * Tickets module on_get_project_object_types events handler
   *
   * @package activeCollab.modules.tickets
   * @subpackage handlers
   */
  
  /**
   * Return tickets module project object types
   *
   * @param void
   * @return string
   */
  function tickets_handle_on_get_project_object_types() {
    return 'ticket';
  } // tickets_handle_on_get_project_object_types

?>