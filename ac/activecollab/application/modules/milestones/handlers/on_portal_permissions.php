<?php

	/**
	 * Milestones handle on_portal_permissions event
	 *
	 * @package activeCollab.modules.milestones
	 * @subpackage handlers
	 */
	
	/**
	 * Handle on_portal_permissions event
	 *
	 * @param array $permissions
	 * @return null
	 */
	function milestones_handle_on_portal_permissions(&$permissions) {
		$permissions[] = 'milestone';
	} // milestones_handle_on_portal_permissions

?>