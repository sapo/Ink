<?php

  /**
   * Pages module on_milestone_objects_by_visibility event handler
   *
   * @package activeCollab.modules.pages
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
  function pages_handle_on_milestone_objects_by_visibility(&$milestone, &$objects, $visibility) {
    $objects[lang('Pages')] = Pages::findByMilestone($milestone, STATE_VISIBLE, $visibility);
  } // pages_handle_on_milestone_objects_by_visibility

?>