<?php

  /**
   * on_master_categories handler definition
   *
   * @package activeCollab.modules.files
   * @subpackage handlers
   */

  /**
   * Handle on_master_categories event
   *
   * @param array $categories
   * @return null
   */
  function files_handle_on_master_categories(&$categories) {
  	$categories[] = array(
  	  'name'       => 'file_categories',
  	  'label'      => lang('File categories'),
  	  'value'      => ConfigOptions::getValue('file_categories'),
  	  'module'     => FILES_MODULE,
  	  'controller' => 'files',
  	);
  } // files_handle_on_master_categories

?>