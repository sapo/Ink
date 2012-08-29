<?php

  // Foundation...
  use_controller('application', SYSTEM_MODULE);

  /**
   * Attachments controller
   *
   * @package activeCollab.modules.resources
   * @subpackage controllers
   */
  class AttachmentsController extends ApplicationController {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = RESOURCES_MODULE;
    
    /**
     * Selected attachment
     *
     * @var Attachment
     */
    var $active_attachment;
    
    /**
     * API actions
     *
     * @var array
     */
    var $api_actions = 'view';
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return AttachmentsController
     */
    function __construct($request) {
      parent::__construct($request);
            
      $attachment_id = $this->request->getId('attachment_id');
      if($attachment_id) {
        $this->active_attachment = Attachments::findById($attachment_id);
      } // if
      
      if(!instance_of($this->active_attachment, 'Attachment')) {
        $this->active_attachment = new Attachment();
      } // if
      
      $this->smarty->assign(array(
        'active_attachment' => $this->active_attachment,
      ));
      
    } // __construct
    
    /**
     * View single attachment (basically, load it and forward it to the user)
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_attachment->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_attachment->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      // Fix problem with non-ASCII characters in IE
      $filename = $this->active_attachment->getName();
      if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
        $filename = urlencode($filename);
      } // if
      
      $as_attachment = $this->request->get('disposition', 'attachment') == 'attachment';
      
      download_file($this->active_attachment->getFilePath(), $this->active_attachment->getMimeType(), $filename, $as_attachment);
      die();
    } // view
    
    /**
     * Show and process edit attachment form
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->active_attachment->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $parent = $this->active_attachment->getParent();
      if(!instance_of($parent, 'ProjectObject')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $attachment_data = $this->request->post('attachment');
      if(!is_array($attachment_data)) {
        $attachment_data = array('name' => $this->active_attachment->getName());
      } // if
      
      $this->smarty->assign('attachment_data', $attachment_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $old_name = $this->active_attachment->getName();
        $this->active_attachment->setName(array_var($attachment_data, 'name'));
        $save = $this->active_attachment->save();
        
        if($save && !is_error($save)) {
          db_commit();
          $this->active_attachment->ready();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('File :filename has been updated', array('filename' => $old_name));
            $this->redirectToUrl($parent->getViewUrl());
          } else {
            $this->serveData($this->active_attachment);
          } // if
        } else {
          db_rollback();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_error('Failed to update :filename', array('filename' => $old_name));
            $this->redirectToUrl($parent->getViewUrl());
          } else {
            $this->serveData($save);
          } // if
        } // if
      } // if
    } // edit
    
    /**
     * Delete attachment
     * 
     * @param void
     * @return null
     */
    function delete() {
      if($this->active_attachment->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $parent = $this->active_attachment->getParent();
      if(!instance_of($parent, 'ProjectObject')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if (!$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      $delete = $this->active_attachment->delete();
      if ($delete && !is_error($delete)) {
        if($this->request->isAsyncCall()) {
          $this->httpOk();
        } else {
          $this->redirectToReferer($parent->getViewUrl());
        } // if
      } else {
        if ($this->request->isAsyncCall()) {
          $this->httpError(HTTP_ERR_OPERATION_FAILED, $delete->getMessage());
        } else {
          flash_error($delete->getMessage());
          $this->redirectToReferer($parent->getViewUrl());
        } // if        
      } // if
    } // delete
  
  } // AttachmentsController

?>