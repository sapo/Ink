<?php

	/**
	 * Discussions module on_portal_milestone_objects event handler
	 *
	 * @package activeCollab.modules.discussions
	 * @subpackage handlers
	 */
	
	/**
	 * Populate $portal_objects with objects which aren't private
	 *
	 * @param Milestone $milestone
	 * @param array $portal_objects
	 * @param Portal $portal
	 * @return null
	 */
	function discussions_handle_on_portal_milestone_objects(&$milestone, &$portal_objects, &$portal) {
		if($portal->getProjectPermissionValue('discussion') >= PROJECT_PERMISSION_ACCESS) {
			$portal_objects[lang('Discussions')] = Discussions::findByMilestone($milestone, STATE_VISIBLE, VISIBILITY_NORMAL); // used existing find method
		} // if
	} // discussions_handle_on_portal_milestone_objects

?>