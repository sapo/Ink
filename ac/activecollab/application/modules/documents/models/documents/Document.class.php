<?php

  /**
   * Document class
   * 
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class Document extends BaseDocument {
  
    /**
     * Cached category instance
     *
     * @var DocumentCategory
     */
    var $category = false;
    
    /**
     * Cached file path value
     *
     * @var string
     */
    var $file_path = false;
    
    /**
     * Return parent category
     *
     * @param void
     * @return DocumentCategory
     */
    function getCategory() {
    	if ($this->category === false){
    	  $this->category = DocumentCategories::findByDocument($this);
    	} // if
    	return $this->category->getId();
    } // getCategory
    
    /**
     * Return full path to the file on disk
     *
     * @param void
     * @return string
     */
    function getFilePath() {
      if($this->file_path === false) {
        $this->file_path = UPLOAD_PATH . '/' . $this->getBody();
      } // if
      return $this->file_path;
    } // getFilePath
    
    /**
     * Return file size
     *
     * @param void
     * @return integer
     */
    function getSize() {
    	$file = $this->getBody();
    	return (filesize(UPLOAD_PATH.'/'.$file));
    } // getSize
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can view specific document
     *
     * @param User $user
     * @return boolean
     */
    function canView($user) {
      return $this->getVisibility() == VISIBILITY_PRIVATE ? $user->canSeePrivate() : true;
    } // canView
    
    /**
     * Returns true if $user can add documents
     *
     * @param User $user
     * @return boolean
     */
    function canAdd($user) {
    	return $user->isAdministrator() || $user->getSystemPermission('can_add_documents') && (boolean) DocumentCategories::findAll($user);
    } // canAdd

    /**
     * Returns true if $user can edit this document
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      return ($user->isAdministrator() || ($user->getId() == $this->getCreatedById()));
    } // canEdit

    /**
     * Returns true if $user can delete this document
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      return ($user->isAdministrator() || ($user->getId() == $this->getCreatedById()));
    } // canDelete
    
    /**
     * Returns true if $user can pin/unpin this document
     *
     * @param User $user
     * @return boolean
     */
    function canPinUnpin($user) {
    	return $user->isAdministrator();
    } // canPin
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view text document URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
      return assemble_url('document_view', array(
        'category_id' => $this->getCategoryId(),
        'document_id' => $this->getId(),
      ));
    } // getViewUrl
    
    /**
     * Return file document URL
     *
     * @param void
     * @return string
     */
    function getPreviewUrl() {
      if(CREATE_THUMBNAILS && in_array($this->getMimeType(), array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png')) && (filesize($this->getFilePath()) < RESIZE_SMALLER_THAN)) {
      	$preview_path = Thumbnails::create($this->getFilePath(), $this->getId() . '-735x500', 735, 500);
  	    if($preview_path) {
  	      return Thumbnails::getUrl(basename($preview_path));
  	    } // if
      } // if
	    return '';
    } // getPreviewUrl
    
    /**
     * Return edit document URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
    	return assemble_url('document_edit', array(
    		'document_id' => $this->getId(),
    	));
    } // getEditUrl
    
    /**
     * Return delete document URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
    	return assemble_url('document_delete', array(
    		'document_id' => $this->getId(),
    	));
    }
    
    /**
     * Return pin document URL
     *
     * @param void
     * @return string
     */
    function getPinUrl() {
    	return assemble_url('document_pin', array(
        'document_id' => $this->getId(),
    	));
    }
    
    /**
     * Return unpin document URL
     *
     * @param void
     * @return string
     */
    function getUnpinUrl() {
    	return assemble_url('document_unpin', array(
        'document_id' => $this->getId(),
    	));
    }
    
    /**
     * Return thumbnail URL
     *
     * @param void
     * @return string
     */
    function getThumbnailUrl() {
      $mime_type = $this->getMimeType();
      
      $start = substr($mime_type, 0, strpos($mime_type, '/'));
      switch($start) {
        case 'application':
          switch($mime_type) {
            case 'application/x-diskcopy':
        	    return get_image_url('types/disk-image.gif');
        	  case 'application/pdf':
        	    return get_image_url('types/document-pdf.gif');
        	  default:
        	    $extension = strtolower(get_file_extension($this->getName()));
        	    if($extension) {
        	      switch($extension) {
        	        case 'psd':
        	          return get_image_url('types/document-psd.gif');
        	        case 'ai':
        	          return get_image_url('types/document-ai.gif');
        	        case 'fla':
        	        case 'flv':
        	        case 'swf':
        	          return get_image_url('types/document-fla.gif');
        	        case 'doc':
        	          return get_image_url('types/document-doc.gif');
        	        case 'xls':
        	          return get_image_url('types/document-xls.gif');
        	        case 'ppt':
        	          return get_image_url('types/document-ppt.gif');
        	        case 'zip':
        	        case 'gz':
        	        case 'tar':
        	        case 'rar':
        	        case 'ace':
        	        case '7z':
        	        case 'sit':
        	          return get_image_url('types/archive.gif');
        	      } // switch
        	    } // if
        	    
        	    return get_image_url('types/blank.gif');
          } // switch
        case 'audio':
          return get_image_url('types/audio.gif');
        case 'image':
          if(CREATE_THUMBNAILS && in_array($mime_type, array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png')) && (filesize($this->getFilePath()) < RESIZE_SMALLER_THAN)) {
            $thumbnail_path = Thumbnails::create($this->getFilePath(), $this->getId() . '-80x80', 80, 80);
      	    if($thumbnail_path) {
      	      return Thumbnails::getUrl(basename($thumbnail_path));
      	    } // if
          } // if
    	    return get_image_url('types/image.gif');
    	  case 'text':
          return get_image_url('types/text.gif');
        case 'video':
          return get_image_url('types/video.gif');
        default:
          return get_image_url('types/blank.gif');
      } // if
    } // getThumbnailUrl
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Set field value
     * 
     * If we are setting body purifier will be included and value will be ran 
     * through it. Else we will simply inherit behavior
     *
     * @param string $field
     * @param mixed $value
     * @return string
     */
    function setFieldValue($field, $value) {
      if(!$this->is_loading && $this->getType() == 'text' && ($field == 'body')) {
        $value = prepare_html($value, true);
      } // if
      
      return parent::setFieldValue($field, $value);
    } // setFieldValue
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
    	if(!$this->validatePresenceOf('name', 3)) {
    		$errors->addError(lang('Document name must be at least 3 characters'), 'name');
    	} // if
    	
    	if(!$this->validatePresenceOf('category_id')) {
    	  $errors->addError(lang('Category is required'), 'category_id');
    	} // if
    	
    	parent::validate($errors, true);
    }
    
    /**
     * Delete document
     * 
     * @param void
     * @return null 
     */
    function delete() {
      $filepath = $this->getFilePath();
      
      db_begin_work();
      
      $delete = parent::delete();
      if (!$delete || is_error($delete)) {
        db_rollback();
        return $delete;
      } // if
      
      $delete_attachments = Attachments::deleteByObject($this);
      if (!$delete_attachments || is_error($delete_attachments)) {
        db_rollback();
        return $delete_attachments;
      } // if
      
      if (is_file($filepath)) {
        @unlink($filepath);
      } // if
      
      db_commit();
      return true;        
    } // delete
  
  }

?>