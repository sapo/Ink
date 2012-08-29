<?php

  /**
   * System handle daily tasks
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Do daily taks
   *
   * @param void
   * @return null
   */
  function system_handle_on_daily() {
    ProjectObjectViews::cleanUp();
    
    // delete all attachments without parent older than 2 days
    Attachments::delete(array('created_on < ? AND (parent_id IS NULL OR parent_id = 0)', new DateTimeValue('-2 days')));
  } // system_handle_on_daily

?>