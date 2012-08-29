<?php

	/**
	 * Tickets module on_portal_milestone_add_links event handler
	 *
	 * @package activeCollab.modules.tickets
	 * @subpackage handlers
	 */
	
	/**
	 * Populate $links with add ticket URL via public portal
	 *
	 * @param Milestone $milestone
	 * @param array $links
	 * @param Portal $portal
	 * @return null
	 */
	function tickets_handle_on_portal_milestone_add_links($milestone, &$links, &$portal) {
		$links[lang('Ticket')] = portal_tickets_module_add_ticket_url($portal, array('milestone_id' => $milestone->getId()));
	} // tickets_handle_on_portal_milestone_add_links

?>