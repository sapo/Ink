<?php

	/**
	 * Tickets module on_portal_milestone_objects event handler
	 *
	 * @package activeCollab.modules.tickets
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
	function tickets_handle_on_portal_milestone_objects(&$milestone, &$portal_objects, &$portal) {
		if($portal->getProjectPermissionValue('ticket') >= PROJECT_PERMISSION_ACCESS) {
			$portal_objects[lang('Tickets')] = Tickets::findByMilestone($milestone, STATE_VISIBLE, VISIBILITY_NORMAL); // used existing find method
		} // if
	} // tickets_handle_on_portal_milestone_objects

?>