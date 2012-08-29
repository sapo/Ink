<?php

  /**
   * Menu group
   *
   * @author Ilija Studen <ilija.studen@gmail.com>
   */
  class MenuGroup extends AngieObject {
    
    /**
     * Array of menu items
     *
     * @var array
     */
    var $items = array();
  
    /**
     * Add item to the group
     *
     * @param MenuItem $item
     * @return null
     */
    function addItem($item) {
      $this->items[$item->name] = $item;
    } // addItem
    
    /**
     * Return number of items
     *
     * @param void
     * @return integer
     */
    function countItems() {
      return count($this->items);
    } // countItems
  
  } // MenuGroup

?>