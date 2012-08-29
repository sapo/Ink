<?php

  /**
   * Discussions module on_get_project_object_types event handler
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */
  
  /**
   * Return discussions module project object types
   *
   * @param void
   * @return string
   */
  function discussions_handle_on_get_project_object_types() {
    return 'discussion';
  } // discussions_handle_on_get_project_object_types

?>