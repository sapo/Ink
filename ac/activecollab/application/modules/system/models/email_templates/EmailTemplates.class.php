<?php

  /**
   * EmailTemplates class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class EmailTemplates extends BaseEmailTemplates {
  
    /**
     * Return email templates grouped by module name
     *
     * @param void
     * @return array
     */
    function findGrouped() {
    	$all = EmailTemplates::find(array(
    	  'order' => 'module, name'
    	));
    	
    	$result = null;
    	if(is_foreachable($all)) {
    	  $result = array();
    	  foreach($all as $template) {
    	    $module = $template->getModule();
    	    if(!isset($result[$module])) {
    	      $result[$module] = array();
    	    } // if
    	    
    	    $result[$module][] = $template;
    	  } // foreach
    	} // if
    	
    	return $result;
    } // findGrouped
    
    /**
     * Delete templates by module name
     *
     * @param string $name
     * @return boolean
     */
    function deleteByModule($name) {
    	$delete = EmailTemplates::delete(array('module = ?', $name));
    	if($delete && !is_error($delete)) {
    	  db_execute('DELETE FROM ' . TABLE_PREFIX . 'email_template_translations WHERE module = ?', $name);
    	} // if
    	return $delete;
    } // deleteByModule
    
    // ---------------------------------------------------
    //  Renderers
    // ---------------------------------------------------
    
    /**
     * Render project object details
     *
     * @param ProjectObject $object
     * @param array $languages
     * @return string
     */
    function renderProjectObjectDetails($object, $languages = null) {
      static $smarty = null;
      
      if($smarty === null) {
        $smarty =& Smarty::instance();
      } // if
      
      $smarty->assign(array(
        '_object' => $object, 
        '_language' => null
      ));
      
      if(is_foreachable($languages)) {
        $result = array();
        foreach($languages as $language) {
          $smarty->assign('_language', $language);
          $result[$language->getLocale()] = $smarty->fetch(get_template_path('_notification_details', null, SYSTEM_MODULE));
        } // forech
        return $result;
      } else {
        return $smarty->fetch(get_template_path('_notification_details', null, SYSTEM_MODULE));
      } // if
    } // renderProjectObjectDetails
  
  }

?>