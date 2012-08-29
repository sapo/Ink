<?php

  /**
   * Wireframe class
   *
   * This class is used to collect and render properties of activeCollab main 
   * layout - wireframe. Current wireframe supports:
   * 
   * - bread crumbs
   * - page tabs
   * - page actions
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Wireframe extends AngieObject {
    
    /**
     * Page details (displayed beneath page title if present)
     *
     * @var string
     */
    var $details;
  
    /**
     * Bread crumbs
     *
     * @var array
     */
    var $bread_crumbs = array();
    
    /**
     * Array of rss feeds
     *
     * @var array
     */
    var $rss_feeds = array();
    
    /**
     * Page actions
     *
     * @var array
     */
    var $page_actions = array();
    
    /**
     * List of messages that are shown below the title, but above content
     *
     * @var array
     */
    var $page_messages = array();
    
    /**
     * Company that is displayed in the page name
     *
     * @var Company
     */
    var $page_company = null;
    
    /**
     * Project that is displayed in the page name
     *
     * @var Project
     */
    var $page_project = null;
    
    /**
     * Print button flag
     * 
     * Possible values:
     * 
     * - true - Show the button and use style sweetcher
     * - false - Don't show the button
     * - URL - Show the button but use special print page
     *
     * @var mixed
     */
    var $print_button = true;
    
    /**
     * Current menu item
     *
     * @var string
     */
    var $current_menu_item;
    
    /**
     * Add single bread crumb
     *
     * @param string $text
     * @param string $url
     * @return null
     */
    function addBreadCrumb($text, $url = null) {
      $this->bread_crumbs[] = array(
        'text' => $text,
        'url' => $url,
      );
    } // addBreadCrumb
    
    /**
     * Add RSS feeds to page
     *
     * @param string $title
     * @param string $url
     * @param string $feed_type
     */
    function addRssFeed($title, $url, $feed_type='application/rss+xml') {
      $this->rss_feeds[] = array(
        'title' => $title,
        'url'   => $url,
        'feed_type' => $feed_type,
      );
    } // addRssFeed
    /**
     * Add page action
     *
     * @param string $text
     * @param string $url
     * @param array $subitems
     * @param array $additional
     * @param integer $weight
     * @return null
     */
    function addPageAction($text, $url, $subitems = null, $additional = null, $weight = 0) {
      if(!isset($this->page_actions[$weight])) {
        $this->page_actions[$weight] = array();
      } // if
      
      $this->page_actions[$weight][] = array(
        'text'     => $text, 
        'url'      => $url,
        'subitems' => $subitems,
        'id'       => array_var($additional, 'id'),
        'method'   => array_var($additional, 'method'),
        'confirm'  => array_var($additional, 'confirm')
      );
    } // addPageAction
    
    /**
     * Return sorted page actions
     *
     * @param void
     * @return array
     */
    function getSortedPageActions() {
      ksort($this->page_actions);
      
      $result = array();
      foreach($this->page_actions as $actions_by_weight) {
        foreach($actions_by_weight as $k => $page_action) {
          $id = isset($page_action['id']) ? $page_action['id'] : $k;
          $result[$id] = $page_action;
        } // foreach
      } // if
      
      return $result;
    } // getSortedPageActions
    
    /**
     * Add page message
     *
     * @param string $body
     * @param string $class
     * @return null
     */
    function addPageMessage($body, $class = 'info') {
      $this->page_messages[] = array(
        'body'  => $body,
        'class' => $class,
        'icon'  => get_image_url("messages/$class.gif"),
      );
    } // addPageMessage
    
    /**
     * Return wireframe instance
     *
     * @param void
     * @return Wireframe
     */
    function &instance() {
      static $instance;
      if($instance === null) {
        $instance = new Wireframe();
      } // if
      return $instance;
    } // instance
  
  } // Wireframe

?>