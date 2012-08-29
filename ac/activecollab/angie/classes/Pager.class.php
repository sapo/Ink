<?php

  /**
   * Pager class
   * 
   * Every instance is used to describe a state of paginated result - number of 
   * total pages, current page and how many projects are listed per page
   */
  class Pager extends AngieObject {
    
    /**
     * Total number of items that will be shown on pages
     *
     * @var integer
     */
    var $total_items = 0;
    
    /**
     * Number of items per page
     *
     * @var integer
     */
    var $items_per_page = 10;
    
    /**
     * Current page NUM
     *
     * @var integer
     */
    var $current_page = 1;
    
    /**
     * Cached last page value. If null the value will be calculated by the
     * getLastPage() function and saved
     *
     * @var integer
     */
    var $last_page = null;
    
    /**
     * Construct pager object
     *
     * @param integer $current_page Current page NUM
     * @param integer $total_items Total items that need to be listed on
     *   pages
     * @param integer $per_page Items per page...
     * @return Pager
     */
    function __construct($current_page, $total_items, $per_page) {
      $this->current_page = $current_page;
      $this->total_items = $total_items;
      $this->items_per_page = $per_page;
    } // __construct
    
    /**
     * Return number of the item that will be first on specific page. This
     * function needs the number of first element (default is 0, for arrays
     * and mysql queryies...)
     *
     * @param integer $page On which page? If null current will be used
     * @param integer $index_from Index of first element in array of items
     * @return integer
     */
    function getFirstPageItemId($page = null, $index_from = 0) {
      $page = is_null($page) ? $this->getCurrentPage() : (integer) $page;
      $index_from = (integer) $index_from;
      
      $page -= 1;
      
      return ($page * $this->getItemsPerPage()) + $index_from;
    } // getFirstPageItemId
    
    // ---------------------------------------------------
    //  Logic
    // ---------------------------------------------------
    
    /**
     * Check if specific page is current page. If $page is null function will use
     * current page
     *
     * @param integer $page Page that need to be checked. If null function will
     *   use current page
     * @return boolean
     */
    function isCurrent($page = null) {
      $page = is_null($page) ? $this->getCurrentPage() : (integer) $page;
      return $page == $this->getCurrentPage();
    } // isCurrent
    
    /**
     * Check if specific page is first page. If $page is null function will use
     * current page
     *
     * @param integer $page Page that need to be checked. If null function will
     *   use current page
     * @return boolean
     */
    function isFirst($page = null) {
      $page = is_null($page) ? $this->getCurrentPage() : (integer) $page;
      return $page == 1;
    } // isFirst
    
    /**
     * Check if specific page is last page. If $page is null function will use
     * current page
     *
     * @param integer $page Page that need to be checked. If null function will
     *   use current page
     * @return boolean
     */
    function isLast($page = null) {
      $page = is_null($page) ? $this->getCurrentPage() : (integer) $page;
      if(is_null($last = $this->getLastPage())) {
        return false;
      } // if
      return $page == $last;
    } // isLast
    
    /**
     * Check if specific page has previous page. If $page is null function will use
     * current page
     *
     * @param integer $page Page that need to be checked. If null function will
     *   use current page
     * @return boolean
     */
    function hasPrevious($page = null) {
      $page = is_null($page) ? $this->getCurrentPage() : (integer) $page;
      return $page > 1;
    } // hasPrevious
    
    /**
     * Check if specific page has next page. If $page is null function will use
     * current page
     *
     * @param integer $page Page that need to be checked. If null function will
     *   use current page
     * @return boolean
     */
    function hasNext($page = null) {
      $page = is_null($page) ? $this->getCurrentPage() : (integer) $page;
      if(is_null($last = $this->getLastPage())) {
        return false;
      } // if
      return $page < $last;
    } // hasNext
    
    /**
     * Stupid method, returns 1. If someone get cerried away :)
     *
     * @param void
     * @return integer
     */
    function getFirstPage() {
      return 1;
    } // getFirstPage
    
    /**
     * Return num of last page
     *
     * @param void
     * @return integer
     */
    function getLastPage() {
      if(is_int($this->last_page)) {
        return $this->last_page;
      } // if
      
      if(($this->getItemsPerPage() < 1) || ($this->getTotalItems() < 1)) {
        return 0;
      } // if
      
      if(($this->getTotalItems() % $this->getItemsPerPage()) == 0) {
        $this->last_page = (integer) ($this->getTotalItems() / $this->getItemsPerPage());
      } else {
        $this->last_page = (integer) ($this->getTotalItems() / $this->getItemsPerPage()) + 1; 
      } // if
      
      return $this->last_page;
    } // getLastPage
    
    /**
     * Return the number of the last item on specific page. If $page is null
     * current page will be used
     *
     * @param integer $page Selected page
     * @param integer $index_from Index of first element in item array
     * @return integer
     */
    function getLastPageItemId($page = null, $index_from = 0) {
      $page = is_null($page) ? $this->getCurrentPage() : (integer) $page;
      $index_from = (integer) $index_from;
      
      $offset = $index_from - 1;
      
      return ($page * $this->getItemsPerPage()) + $offset;
    } // getLastPageItemId
    
    /**
     * Return num of previous page, if there is not previous page return NULL
     *
     * @param void
     * @return integer
     */
    function getPreviousPage() {
      $current = $this->getCurrentPage();
      return $current > 1 ? $current - 1 : null;
    } // end funct getPreviousPage
    
    /**
     * Returns num of last page... If there is no last page (we are on it) return
     * NULL
     *
     * @param void
     * @return integer
     */
    function getNextPage() {
      if(is_null($last = $this->getLastPage())) {
        return null;
      } // if
      $current = $this->getCurrentPage();
      
      return $current < $last ? $current + 1 : null;
    } // getNextPage
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return total items value
     *
     * @param void
     * @return integer
     */
    function getTotalItems() {
      return $this->total_items;
    } // getTotalItems
    
    /**
     * Set total items value
     *
     * @param integer $value New value, must be > 1
     * @return boolean
     */
    function setTotalItems($value) {
      $value = (integer) $value;
      if($value < 1) {
        return false;
      } // if
      $this->total_items = $value;
      $this->last_page = null;
      return true;
    } // setTotalItems
    
    /**
     * Return items per page value
     *
     * @param void
     * @return integer
     */
    function getItemsPerPage() {
      return $this->items_per_page;
    } // getItemsPerPage
    
    /**
     * Set items per page value
     *
     * @param integer $value New value
     * @return boolean
     */
    function setItemsPerPage($value) {
      $value = (integer) $value;
      if($value < 1) {
        return false;
      } // if
      $this->items_per_page = $value;
      $this->last_page = null;
      return true;
    } // setItemsPerPage
    
    /**
     * Return current page value
     *
     * @param void
     * @return integer
     */
    function getCurrentPage() {
      return $this->current_page;
    } // getCurrentPage
    
    /**
     * Set current page value
     *
     * @param integer $value New value, must be > 1
     * @return boolean
     */
    function setCurrentPage($value) {
      $value = (integer) $value;
      
      if($value < 1) {
        return false;
      } // if
      
      $this->current_page = $value;
      return true;
    } // setCurrentPage
    
  } // end class Pager

?>