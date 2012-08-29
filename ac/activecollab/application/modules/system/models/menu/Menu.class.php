<?php

  /**
   * Menu class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Menu extends AngieObject {
    
    /**
     * Menu groups
     *
     * @var array
     */
    var $groups = array();
    
    /**
     * Create new group
     * 
     * Add multiple groups to the menu. Example:
     * 
     * $menu->add('group_1', 'group_2', 'group_3');
     *
     * @param void
     * @return null
     */
    function addGroup() {
      $group_names = func_get_args();
      foreach($group_names as $group_name) {
        if(!isset($this->groups[$group_name])) {
          $this->groups[$group_name] = new MenuGroup();
        } // if
      } // if
    } // addGroup
    
    /**
     * Add item to specific group
     * 
     * $items is a single MenuItem instance or array of menu items
     *
     * @param array $items
     * @param string $group_name
     * @return null
     */
    function addToGroup($items, $group_name) {
      $this->addGroup($group_name); // make sure that $group_name group exists
      
      if(!is_array($items)) {
        $items = array($items);
      } // if
      
      foreach($items as $item) {
        $this->groups[$group_name]->addItem($item);
      } // if
    } // addToGroup
  
  } // Menu

?>