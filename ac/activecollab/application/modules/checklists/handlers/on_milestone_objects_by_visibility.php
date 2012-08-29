<?php

  /**
   * Checklists module on_milestone_objects_by_visibility event handler
   *
   * @package activeCollab.modules.checklists
   * @subpackage handlers
   */

  /**
   * Populate $objects with objects that are in $visibility domain
   *
   * @param Milestone $milestone
   * @param array $objects
   * @param integer $visibility
   * @return null
   */
  function checklists_handle_on_milestone_objects_by_visibility(&$milestone, &$objects, $visibility) {
    $objects[lang('Checklists')] = Checklists::findByMilestone($milestone, STATE_VISIBLE, $visibility);
  } // checklists_handle_on_milestone_objects_by_visibility

?>