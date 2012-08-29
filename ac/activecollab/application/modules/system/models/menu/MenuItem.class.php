<?php

  /**
   * Main menu item
   *
   * @author Ilija Studen <ilija.studen@gmail.com>
   */
  class MenuItem extends AngieObject {
    
    /**
     * Unique item name
     *
     * @var string
     */
    var $name;
    
    /**
     * Item caption
     *
     * @var string
     */
    var $caption;
    
    /**
     * Item URL
     *
     * @var string
     */
    var $url;
    
    /**
     * Item icon URL
     *
     * @var string
     */
    var $icon_url;
    
    /**
     * Value of menu item badge
     *
     * @var mixed
     */
    var $badge_value;
    
    /**
     * Access key
     *
     * @var string
     */
    var $access_key;
  
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $url
     * @param string $icon_url
     * @param array $subitems
     * @param mixed $badge_value
     * 
     * @return MenuItem
     */
    function __construct($name, $caption, $url, $icon_url, $badge_value = null, $access_key = null) {
      $this->name = $name;
      $this->caption = $caption;
      $this->url = $url;
      $this->icon_url = $icon_url;
      $this->badge_value = $badge_value;
      $this->access_key = $access_key;
    } // __construct
    
    /**
     * Render this item
     *
     * @param void
     * @return null
     */
    function render() {
      $rendered = '<a href="' . $this->url . '" class="main"';
      if ($this->access_key) {
        $rendered.= ' accesskey="' . $this->access_key . '"';
      } // if
      $rendered.='><span class="outer"><span class="inner" style="background-image: url(\'' . $this->icon_url . '\')">';
      if ($this->badge_value) {
        $rendered.= '<span class="badge">' . clean($this->badge_value). '</span>';
      }
      $rendered.= clean($this->caption) . '</span></span></a>';
      
      return $rendered;
    } // render
  
  } // MenuItem

?>