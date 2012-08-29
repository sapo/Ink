<?php

	/**
	 * Tickets handle on_portal_permissions event
	 *
	 * @package activeCollab.modules.tickets
	 * @subpackage handlers
	 */
	
	/**
	 * Handle on_portal_permissions event
	 *
	 * @param array $permissions
	 * @return null
	 */
	function tickets_handle_on_portal_permissions(&$permissions) {
		$permissions[] = 'ticket';
	} // tickets_handle_on_portal_permissions

?>