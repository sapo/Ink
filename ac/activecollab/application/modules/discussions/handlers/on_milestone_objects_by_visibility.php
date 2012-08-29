<?php

  /**
   * Discussions module on_milestone_objects_by_visibility event handler
   *
   * @package activeCollab.modules.discussions
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
  function discussions_handle_on_milestone_objects_by_visibility(&$milestone, &$objects, $visibility) {
    $objects[lang('Discussions')] = Discussions::findByMilestone($milestone, STATE_VISIBLE, $visibility);
  } // discussions_handle_on_milestone_objects_by_visibility

?>