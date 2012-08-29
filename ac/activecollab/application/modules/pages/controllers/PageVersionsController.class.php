<?php

  use_controller('pages', PAGES_MODULE);

  /**
   * Page versions controller
   *
   * @package activeCollab.modules.pages
   * @subpackage controllers
   */
  class PageVersionsController extends PagesController {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = PAGES_MODULE;
    
    /**
     * Selected page version
     *
     * @var PageVersion
     */
    var $active_page_version;
    
    /**
     * Construct page versions controller
     *
     * @param Request $request
     * @return PageVersionsController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if($this->active_page->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $version = $this->request->getId('version');
      if($version) {
        $this->active_page_version = PageVersions::findById(array(
          'page_id' => $this->active_page->getId(),
          'version' => $version,
        ));
      } // if
      
      if(!instance_of($this->active_page_version, 'PageVersion')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->smarty->assign(array(
        'active_page_version' => $this->active_page_version,
      ));
    } // __construct
    
    /**
     * Delete version
     *
     * @param void
     * @return null
     */
    function delete() {
      if($this->request->isSubmitted()) {
        if(!$this->active_page_version->canDelete($this->logged_user)) {
          $this->httpError(HTTP_ERR_FORBIDDEN);
        } // if
        
        $delete = $this->active_page_version->delete();
        if($delete && !is_error($delete)) {
          if($this->request->isAsyncCall()) {
            $this->httpOk();
          } else {
            flash_success('Version #:version has been deleted', array('version' => $this->active_page_version->getVersion()));
          } // if
        } else {
          if($this->request->isAsyncCall()) {
            $this->httpError(HTTP_ERR_OPERATION_FAILED);
          } else {
            flash_success('Failed to delete version #:version', array('version' => $this->active_page_version->getVersion()));
          } // if
        } // if
        
        $this->redirectToUrl($this->active_page->getViewUrl());
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // delete
    
  }

?>