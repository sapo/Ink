<?php

	/**
	 * Discussions module on_portal_milestone_add_links event handler
	 *
	 * @package activeCollab.modules.discussions
	 * @subpackage handlers
	 */
	
	/**
	 * Populate $links with add discussion URL via portal
	 *
	 * @param Milestone $milestone
	 * @param array $links
	 * @param Portal $portal
	 * @return null
	 */
	function discussions_handle_on_portal_milestone_add_links($milestone, &$links, &$portal) {
		$links[lang('Discussion')] = portal_discussions_module_add_discussion_url($portal, $milestone->getProject(), array('milestone_id' => $milestone->getId()));
	} // discussions_handle_on_portal_milestone_add_links

?>