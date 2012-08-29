<?php

  /**
   * Checklists module on_copy_project_items handler
   *
   * @package activeCollab.modules.checklists
   * @subpackage handlers
   */

  /**
   * Handle on_copy_project_items event
   *
   * @param Project $from
   * @param Project $to
   * @param array $milestones_map
   * @param array $categories_map
   * @return null
   */
  function checklists_handle_on_copy_project_items(&$from, &$to, $milestones_map, $categories_map) {
    $checklists = Checklists::findByProject($from, STATE_VISIBLE, VISIBILITY_PRIVATE);
  	if(is_foreachable($checklists)) {
  	  foreach($checklists as $checklist) {
  	    $checklist->copyToProject($to, null, null, array('Task'));
  	  } // foreach
  	} // if
  } // checklists_handle_on_copy_project_items

?>