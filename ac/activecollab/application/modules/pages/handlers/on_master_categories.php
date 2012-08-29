<?php

  /**
   * on_master_categories handler definition
   *
   * @package activeCollab.modules.pages
   * @subpackage handlers
   */

  /**
   * Handle on_master_categories event
   *
   * @param array $categories
   * @return null
   */
  function pages_handle_on_master_categories(&$categories) {
  	$categories[] = array(
  	  'name'       => 'pages_categories',
  	  'label'      => lang('Pages categories'),
  	  'value'      => ConfigOptions::getValue('pages_categories'),
  	  'module'     => PAGES_MODULE,
  	  'controller' => 'pages',
  	);
  } // pages_handle_on_master_categories

?>