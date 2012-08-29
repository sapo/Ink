<?php

	/**
	 * Discussions handle on_portal_permissions event
	 *
	 * @package activeCollab.modules.discussions
	 * @subpackage handlers
	 */
	
	/**
	 * Handle on_portal_permissions event
	 *
	 * @param array $permissions
	 * @return null
	 */
	function discussions_handle_on_portal_permissions(&$permissions) {
		$permissions[] = 'discussion';
	} // discussions_handle_on_portal_permissions

?>