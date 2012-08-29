<?php

  /**
   * on_master_categories handler definition
   *
   * @package activeCollab.modules.tickets
   * @subpackage handlers
   */

  /**
   * Handle on_master_categories event
   *
   * @param array $categories
   * @return null
   */
  function tickets_handle_on_master_categories(&$categories) {
  	$categories[] = array(
  	  'name'       => 'ticket_categories',
  	  'label'      => lang('Ticket categories'),
  	  'value'      => ConfigOptions::getValue('ticket_categories'),
  	  'module'     => TICKETS_MODULE,
  	  'controller' => 'tickets',
  	);
  } // tickets_handle_on_master_categories

?>