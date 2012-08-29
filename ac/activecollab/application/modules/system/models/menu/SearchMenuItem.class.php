<?php

  /**
   * Render search menu
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class SearchMenuItem extends MenuItem {
  
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $url
     * @return MenuItem
     */
    function __construct($name, $caption) {
      $this->name = $name;
      $this->caption = $caption;
    } // __construct
    
    /**
     * Render this item
     *
     * @param void
     * @return null
     */
    function render() {
      $q = clean(array_var($_GET, 'q'));
      
      return '<form action="' . assemble_url('search') . '" method="get" id="searchForm">
        <div class="inner">
          <input type="text" name="q" class="text" value="' . $q . '" />
          <div>
            <button type="submit"><span><span>' . clean($this->caption) . '</span></span></button>
          </div>
        </div>
      </form>';
    } // render
  
  } // SearchMenuItem

?>