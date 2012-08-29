<?php

	/**
	 * Source handle on_portal_permissions event
	 *
	 * @package activeCollab.modules.source
	 * @subpackage handlers
	 */
	
	/**
	 * Handle on_portal_permissions event
	 *
	 * @param array $permissions
	 * @return null
	 */
	function source_handle_on_portal_permissions(&$permissions) {
		$permissions[] = 'repository';
	} // source_handle_on_portal_permissions

?>