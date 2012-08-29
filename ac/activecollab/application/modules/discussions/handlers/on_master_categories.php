<?php

  /**
   * on_master_categories handler definition
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Handle on_master_categories event
   *
   * @param array $categories
   * @return null
   */
  function discussions_handle_on_master_categories(&$categories) {
  	$categories[] = array(
  	  'name'       => 'discussion_categories',
  	  'label'      => lang('Discussion categories'),
  	  'value'      => ConfigOptions::getValue('discussion_categories'),
  	  'module'     => DISCUSSIONS_MODULE,
  	  'controller' => 'discussions',
  	);
  } // discussions_handle_on_master_categories

?>