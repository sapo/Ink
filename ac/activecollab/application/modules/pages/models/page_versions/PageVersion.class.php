<?php

  /**
   * PageVersion class
   * 
   * @package activeCollab.modules.pages
   * @subpackage models
   */
  class PageVersion extends BasePageVersion {
    
    /**
     * Return formatted body
     *
     * @param void
     * @return string
     */
    function getFormattedBody() {
      return nl2br($this->getBody());
    } // getFormattedBody
    
    /**
     * Return page ID
     *
     * @param void
     * @return Page
     */
    function getPage() {
      return Pages::findById($this->getPageId());
    } // getPage
  
    /**
     * Set parent page
     *
     * @param Page $page
     * @return null
     */
    function setPage($page) {
      $this->setPageId($page->getId());
      if($this->isNew()) {
        $this->setName($page->old_name ? $page->old_name : $page->getName());
        $this->setBody($page->old_body ? $page->old_body : $page->getBody());
        $this->setVersion($page->getRevisionNum());
      } // if
    } // setPage
    
    /**
     * Describe this page version for use in API
     *
     * @param User $user
     * @return array
     */
    function describe($user) {
      return array(
        'version' => $this->getVersion(),
        'name' => $this->getName(),
        'body' => $this->getBody(),
        'created_on' => $this->getCreatedOn(),
        'created_by_id' => $this->getCreatedById(),
      );
    } // describe
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can delete this version
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      return true;
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
      $page = $this->getPage();
      if(instance_of($page, 'Page')) {
        return $page->getCompareVersionsUrl($this);
      } else {
        return '#';
      } // if
    } // getViewUrl
    
    /**
     * Return delete version URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      $page = $this->getPage();
      if(instance_of($page, 'Page')) {
        return assemble_url('project_page_version_delete', array('project_id' => $page->getProjectId(), 'page_id' => $page->getId(), 'version' => $this->getVersion()));
      } else {
        return '#';
      } // if
    } // getDeleteUrl
  
  }

?>