<?php

  // Build on top of administration controller
  use_controller('settings', SYSTEM_MODULE);

  /**
   * Categories settings controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class CategoriesAdminController extends SettingsController {
    
    /**
     * PHP4 safe controller name
     *
     * @var string
     */
    var $controller_name = 'categories_admin';
    
    /**
     * Show and process manage categories page
     *
     * @param void
     * @return null
     */
    function index() {
      $category_definitions = array();
      event_trigger('on_master_categories', array(&$category_definitions));
      
      $this->smarty->assign('category_definitions', $category_definitions);
      
      if($this->request->isSubmitted()) {
        if(is_foreachable($category_definitions)) {
          foreach($category_definitions as $category_definition) {
            $value = $this->request->post($category_definition['name']);
            if(!is_array($value) || (count($value) < 1)) {
              $value = array(lang('General'));
            } // if
            
            ConfigOptions::setValue($category_definition['name'], $value);
          } // foreach
        } // if
        
        flash_success('Master categories have been updated');
        $this->redirectTo('admin');
      } // if
    } // index
    
  }

?>