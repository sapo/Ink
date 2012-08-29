<?php

  /**
  * Page construction class
  *
  * This class is used to describe construction of single HTML document. By 
  * default it is used to set head properties of document, but it can be 
  * extended to support more complex documents (add support for bread crumbs, 
  * page actions, page elements and so on)
  * 
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class PageConstruction extends AngieObject {
    
    /**
    * Page title
    *
    * @var string
    */
    var $page_title;
    
    /**
    * Array of of link tags
    *
    * @var array
    */
    var $links = array();
    
    /**
    * Array of meta tags
    *
    * @var array
    */
    var $meta = array();
    
    /**
    * Array of style elements
    *
    * @var array
    */
    var $style = array();
    
    /**
    * Array of scripts that will be included
    *
    * @var array
    */
    var $scripts = array();
    
    /**
    * Add link to this document
    *
    * @param string $href
    * @param string $rel
    * @param array $attributes
    * @return null
    */
    function addLink($href, $rel = null, $attributes = null) {
      if(isset($this) && instance_of($this, 'PageConstruction')) {
        if(!is_array($attributes)) {
          $attributes = array();
        } // if
        $attributes['href'] = $href;
        
        if($rel !== null) {
          $attributes['rel'] = $rel;
        } // if
        
        $this->links[] = open_html_tag('link', $attributes, true);
      } else {
        $instance =& PageConstruction::instance();
        return $instance->addLink($href, $rel, $attributes);
      } // if
    } // addLink
    
    /**
    * Add meta tag to this page
    *
    * @param string $name
    * @param string $content
    * @param boolean $http_equiv
    * @return null
    */
    function addMeta($name, $content, $http_equiv = false) {
      if(isset($this) && instance_of($this, 'PageConstruction')) {
        $name_attribute = $http_equiv ? 'http-equiv' : 'name';
        $this->meta[] = open_html_tag('meta', array($name_attribute => $name, 'content' => $content), true);
      } else {
        $instance =& PageConstruction::instance();
        return $instance->addMeta($name, $content, $http_equiv);
      } // if
    } // addMeta
    
    /**
    * Add script to this page
    *
    * @param string $content
    * @param boolean $inline
    * @return null
    */
    function addScript($content, $inline = true) {
      if(isset($this) && instance_of($this, 'PageConstruction')) {
        if($inline) {
          $this->scripts[] = open_html_tag('script', array('type' => 'text/javascript')) . $content . '</script>';
        } else {
          $this->scripts[] = open_html_tag('script', array('type' => 'text/javascript', 'src' => $content)) . '</script>';
        } // if
      } else {
        $instance =& PageConstruction::instance();
        return $instance->addScript($content, $inline, $attributes);
      } // if
    } // addScript
    
    // ---------------------------------------------------
    //  Helper methods
    // ---------------------------------------------------
    
    /**
    * Add stylesheet
    *
    * @param string $href
    * @param string $media
    * @return null
    */
    function addStylesheet($href, $media = 'screen') {
      $this->addLink($href, 'stylesheet', array('type' => 'text/css', 'media' => $media));
    } // addStylesheet
    
    /**
    * Return all head tags as one array
    *
    * @param void
    * @return array
    */
    function getAllHeadTags() {
      $result = array();
      if(count($this->links)) {
        $result = array_merge($result, $this->links);
      } // if
      if(count($this->meta)) {
        $result = array_merge($result, $this->meta);
      } // if
      if(count($this->style)) {
        $result = array_merge($result, $this->style);
      } // if
      if(count($this->scripts)) {
        $result = array_merge($result, $this->scripts);
      } // if
      return count($result) ? $result : null;
    } // getAllHeadTags
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Get page_title
    *
    * @param null
    * @return string
    */
    function getPageTitle() {
      if(isset($this) && instance_of($this, 'PageConstruction')) {
        return $this->page_title;
      } else {
        $instance =& PageConstruction::instance();
        return $instance->getPageTitle();
      } // if
    } // getPageTitle
    
    /**
    * Set page_title value
    *
    * @param string $value
    * @return null
    */
    function setPageTitle($value) {
      if(isset($this) && instance_of($this, 'PageConstruction')) {
        $this->page_title = $value;
      } else {
        $instance =& PageConstruction::instance();
        return $instance->setPageTitle($value);
      } // if
    } // setPageTitle
    
    /**
    * Return all page links
    *
    * @param void
    * @return array
    */
    function getLinks() {
      if(isset($this) && instance_of($this, 'PageConstruction')) {
        return $this->links;
      } else {
        $instance =& PageConstruction::instance();
        return $instance->getLinks();
      } // if
    } // getLinks
    
    /**
    * Return all meta tags
    *
    * @param void
    * @return array
    */
    function getMeta() {
      if(isset($this) && instance_of($this, 'PageConstruction')) {
        return $this->meta;
      } else {
        $instance =& PageConstruction::instance();
        return $instance->getMeta();
      } // if
    } // getMeta
    
    /**
    * Return all style tags
    *
    * @param void
    * @return array
    */
    function getStyle() {
      if(isset($this) && instance_of($this, 'PageConstruction')) {
        return $this->style;
      } else {
        $instance =& PageConstruction::instance();
        return $instance->getStyle();
      } // if
    } // getStyle
    
    /**
    * Return all scripts
    *
    * @param void
    * @return array
    */
    function getScripts() {
      if(isset($this) && instance_of($this, 'PageConstruction')) {
        return $this->scripts;
      } else {
        $instance =& PageConstruction::instance();
        return $instance->getScripts();
      } // if
    } // getScripts
    
    /**
    * Return page head instance
    *
    * @param void
    * @return PageConstruction
    */
    function &instance() {
      static $instance;
      if(!instance_of($instance, 'PageConstruction')) {
        $instance = new PageConstruction();
      } // if
      return $instance;
    } // instance
  
  } // PageConstruction

?>