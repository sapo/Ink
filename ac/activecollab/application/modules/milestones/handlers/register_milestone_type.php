<?php

  /**
   * Milestones module register_milestone_type handler
   *
   * @package activeCollab.modules.milestones
   * @subpackage handlers
   */
  
  /**
   * Register milestone type
   * 
   * This is handler that will register milestone type to any event that requires type registration
   *
   * @param void
   * @return string
   */
  function milestones_handle_register_milestone_type() {
    return 'milestone';
  } // milestones_handle_register_milestone_type

?>