<?php

  /**
   * Tickets module on_milestone_objects_by_visibility event handler
   *
   * @package activeCollab.modules.tickets
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
  function tickets_handle_on_milestone_objects_by_visibility(&$milestone, &$objects, $visibility) {
    $objects[lang('Tickets')] = Tickets::findByMilestone($milestone, STATE_VISIBLE, $visibility);
  } // tickets_handle_on_milestone_objects_by_visibility

?>