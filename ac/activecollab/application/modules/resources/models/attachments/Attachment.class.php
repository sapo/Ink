<?php

  /**
   * Attachment row implementation
   *
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Attachment extends BaseAttachment {
    
    /**
     * Name of the route used for view URL
     *
     * @var string
     */
    var $view_route_name = 'attachment_view';
    
    /**
     * Name of the portal route used for view URL
     *
     * @var string
     */
    var $portal_view_route_name = 'portal_attachment_view';
    
    /**
     * Name of the route used for edit URL
     *
     * @var string
     */
    var $edit_route_name = 'attachment_edit';
    
    /**
     * Name of the route used for delete URL
     *
     * @var string
     */
    var $delete_route_name = 'attachment_delete';
    
    /**
     * Name of the object ID variable used alongside project_id when URL-s are 
     * generated (eg. comment_id, task_id, message_category_id etc)
     *
     * @var string
     */
    var $object_id_param_name = 'attachment_id';

    /**
     * Cached file path value
     *
     * @var string
     */
    var $file_path = false;
    
    /**
     * Cached parent object
     *
     * @var ApplicationObject
     */
    var $parent = false;
    
    /**
     * Constructor
     *
     * @param mixed $id
     * @return Attachment
     */
    function __construct($id = null) {
      // $this->setModule(RESOURCES_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Return parent object
     *
     * @param void
     * @return ApplicationObject
     */
    function &getParent() {
      if($this->parent === false) {
        if(strtolower($this->getParentType()) == 'document') {
          $this->parent = $this->getParentId() ? Documents::findById($this->getParentId()) : null;
        } else {
          $this->parent = $this->getParentId() ? ProjectObjects::findById($this->getParentId()) : null;
        } // if
      } // if
      return $this->parent;
    } // getParent
    
    /**
     * Set object parent
     *
     * @param ApplicationObject $parent
     * @param boolean $save
     * @return boolean
     */
    function setParent($parent, $save = false) {
      if($parent === null || instance_of($parent, 'ApplicationObject')) {
        if($parent === null) {
          $this->setParentId(null);
          $this->setParentType(null);
        } else {
          $this->setParentId($parent->getId());
          $this->setParentType(get_class($parent));       
        } // if
      
        return $save ? $this->save() : true;
      } else {
        return false;
      } // if
    } // setParent
    
    /**
     * Find project id
     * 
     * @param void
     * @return integer
     */
    function getProjectId() {
      $parent = $this->getParent();
      if (instance_of($parent, 'ProjectObject')) {
        return $parent->getProjectId();
      } // if
      return false;
    } // getProjectId
    
    /**
     * Return full path to the file on disk
     *
     * @param void
     * @return string
     */
    function getFilePath() {
      if($this->file_path === false) {
        $this->file_path = UPLOAD_PATH . '/' . $this->getLocation();
      } // if
      return $this->file_path;
    } // getFilePath
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view attachment URL
     *
     * @param integer $project_id
     * @param integer $force_download
     * @return string
     */
    function getViewUrl($project_id = null, $force_download = false) {
      $project_id = $project_id ? $project_id : $this->getProjectId();
      $disposition = $force_download ? 'attachment' : ($this->isImage() ? 'inline' : 'attachment');
      
      return assemble_url($this->view_route_name, array(
        'attachment_id' => $this->getId(),
        'disposition' => $disposition,
        'project_id' => $project_id
      ));
    } // getViewUrl
    
    /**
     * Return portal view attachment URL
     *
     * @param Portal $portal
     * @param boolean $force_download
     * @return string
     */
    function getPortalViewUrl($portal, $force_download = false) {
    	$disposition = $force_download ? 'attachment' : ($this->isImage() ? 'inline' : 'attachment');
    	
    	return assemble_url($this->portal_view_route_name, array(
    		'portal_name'   => $portal->getSlug(),
    		'attachment_id' => $this->getId(),
    		'disposition'   => $disposition
    	));
    } // getPortalViewUrl
    
    /**
     * Return delete attachment URL
     * 
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url($this->delete_route_name, array(
        'attachment_id' => $this->getId(),
        'project_id' => $this->getProjectId()
      ));      
    } // getDeleteUrl
    
    /**
     * Return icon URL
     *
     * @param void
     * @return string
     */
    function getIconUrl() {
      return get_image_url('icons/unknown-file-small.gif');
    } // getIconUrl
    
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
        	        case 'doc': case 'docx':
        	          return get_image_url('types/document-doc.gif');
        	        case 'xls': case 'xlsx':
        	          return get_image_url('types/document-xls.gif');
        	        case 'ppt': case 'pptx':
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
    
    /**
     * Returns true if this attacment has a large preview
     *
     * @param void
     * @return boolean
     */
    function hasPreview() {
    	return (boolean) $this->getPreviewUrl();
    } // hasPreview
    
    /**
     * Return large preview URL
     *
     * @param void
     * @return string
     */
    function getPreviewUrl() {
      if(CREATE_THUMBNAILS && $this->isImage() && (filesize($this->getFilePath()) < RESIZE_SMALLER_THAN)) {
      	$preview_path = Thumbnails::create($this->getFilePath(), $this->getId() . '-735x500', 735, 500);
  	    if($preview_path) {
  	      return Thumbnails::getUrl(basename($preview_path));
  	    } // if
      } // if
	    return '';
    } // getPreviewUrl
    
    /**
     * Returns true if this file is image that we can process using GD
     *
     * @param void
     * @return boolean
     */
    function isImage() {
      return in_array($this->getMimeType(), array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png'));
    } // isImage
    
    /**
     * Describe this attachment
     *
     * @param User $user
     * @param array $additional
     * @return array
     */
    function describe($user, $additional = null) {
      return array(
        'id' => $this->getId(),
        'name' => $this->getName(),
        'mime_type' => $this->getMimeType(),
        'size' => (integer) $this->getSize(),
        'created_on' => $this->getCreatedOn(),
        'created_by_id' => $this->getCreatedById(),
        'permalink' => $this->getViewUrl(),
      );
    } // describe
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can access this attachment
     *
     * @param User $user
     * @return boolean
     */
    function canView($user) {
    	$parent = $this->getParent();
    	
    	if(instance_of($parent, 'ProjectObject') || instance_of($parent, 'Document')) {
    	  return $parent->canView($user);
    	} else {
    	  return ($user->getId() == $this->getCreatedById()) || $user->isAdministrator();
    	} // if
    } // canView
    
    /**
     * Returns true if anonymous users can access this attachment via portal
     *
     * @param Portal $portal
     * @return boolean
     */
    function canViewByPortal($portal) {
    	$parent = $this->getParent();
    	return instance_of($parent, 'ProjectObject') && $parent->canViewByPortal($portal);
    } // canViewByPortal
    
    /**
     * Returns true if $user can delete this attachment
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      $parent = $this->getParent();
      if (instance_of($parent, 'ProjectObject')) {
    	  return$parent->canEdit($user);
      } else {
        return ($user->getId() == $this->getCreatedById()) || $user->isAdministrator();
      } // if
    } // canDelete
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Save method
     * 
     * @param void
     * @return boolean
     * @throws DBQueryError
     * @throws ValidationErrors
     *
     */
    function save() {
//      $parent = $this->getParent();
//      if (instance_of($parent, 'ApplicationObject')) {
//        $this->setParentType(get_class($parent));  
//      } // if
      return parent::save();
    } // function
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if(!$this->validatePresenceOf('name', 3)) {
        $errors->addError(lang('File name is required. Min length is 3 letters'), 'name');
      } // if
      
      parent::validate($errors, true);
    } // validate
    
    /**
     * Delete attachment from database and file from disk
     *
     * @param void
     * @return boolean
     */
    function delete() {
      $file = $this->getFilePath();
      
      $delete = parent::delete();
      if($delete && !is_error($delete)) {
        unlink($file);
      } // if
      
      return $delete;
    } // delete
  
  }

?>