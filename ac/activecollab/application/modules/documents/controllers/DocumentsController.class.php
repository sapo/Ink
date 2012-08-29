<?php

  // We need DocumentCategoriesController
  use_controller('document_categories', DOCUMENTS_MODULE);

  /**
   * Documents controller
   *
   * @package activeCollab.modules.documents
   * @subpackage controllers
   */
  class DocumentsController extends DocumentCategoriesController {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = DOCUMENTS_MODULE;
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'documents';
    
    /**
     * Selected Document
     *
     * @var document
     */
    var $active_document;
    
    /**
     * Constructor method
     *
     * @param string $request
     * @return DocumentsController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $document_id = $this->request->getId('document_id');
      if($document_id) {
      	$this->active_document = Documents::findById($document_id);
      } // if
      
      if(instance_of($this->active_document, 'Document')) {
      	$this->wireframe->addBreadCrumb($this->active_document->getName(), $this->active_document->getViewUrl());
      } else {
      	$this->active_document = new Document();
      } // if
      
      $this->wireframe->current_menu_item = 'documents';
      
      $this->smarty->assign(array(
        'active_document' => $this->active_document
      ));
    } // __construct
    
    /**
     * Index page action
     * 
     * @param void
     * @return void
     */
    function index() {
    	$page = (integer) $this->request->get('page');
    	if($page < 1) {
    		$page = 1;
    	} // if
    	
    	$per_page = 10;
    	
    	list($documents, $pagination) = Documents::paginateDocuments($this->logged_user->getVisibility(), $page, $per_page);
    	
    	$this->smarty->assign(array(
    		'documents' => $documents,
    		'pagination' => $pagination,
    	));
    	
    	js_assign(array(
    		'pin_icon_url' => get_image_url('icons/pinned.16x16.gif'),
    		'unpin_icon_url' => get_image_url('icons/not-pinned.16x16.gif')
    	));
    } // index
    
    /**
     * View document page action
     *
     * @param void
     * @return void
     */
    function view() {
    	if($this->active_document->isNew()) {
    		$this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_document->canView($this->logged_user)) {
    		$this->httpError(HTTP_ERR_FORBIDDEN);
    	} // if
    	
    	if($this->active_document->getType() == 'file') {
    	  
    	  // Fix problem with non-ASCII characters in IE
        $filename = $this->active_document->getName();
        if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
          $filename = urlencode($filename);
        } // if
        
      	download_file($this->active_document->getFilePath(), $this->active_document->getMimeType(), $filename, true);
      	
      	die();
    	} else {
    	  if($this->active_document->getVisibility() <= VISIBILITY_PRIVATE) {
    	    $this->wireframe->addPageMessage(lang('<strong>Private</strong> - only members with: :roles roles can see this document.', array('roles' => who_can_see_private_objects(true, lang(' or ')))), 'private');
    	  } // if
    	} // if
    } // view
    
    /**
     * Add text document page action
     * 
     * @param void
     * @return void
     */
    function add_text() {
		  $this->wireframe->print_button = false;
		  
		  if(!Document::canAdd($this->logged_user)) {
		  	$this->httpError(HTTP_ERR_FORBIDDEN);
		  } // if
		  
	    $document_data = $this->request->post('document');
	    if(!is_array($document_data)) {
	    	$document_data = array('category_id' => $this->active_document_category->getId());
	    } // if
	    
	    $this->smarty->assign(array(
	    	'document_data' => $document_data,
	    ));
	    
	    if ($this->request->isSubmitted()) {
	    	db_begin_work();
	    	
	    	$this->active_document->setAttributes($document_data);
	    	$this->active_document->setType('text');
	    	$this->active_document->setCreatedBy($this->logged_user);
				$save = $this->active_document->save();
				
				if ($save && !is_error($save)) {
					$notify_user_ids = $this->request->post('notify_users');
					if(is_foreachable($notify_user_ids)) {
						$notify_users = Users::findByIds($notify_user_ids);
						$owner_company = get_owner_company();
						
						if(is_foreachable($notify_users)) {
								ApplicationMailer::send($notify_users, 'documents/new_text_document', array(
									'document_name' => $this->active_document->getName(),
									'created_by_name' => $this->active_document->getCreatedByName(),
									'created_by_url' => $this->logged_user->getViewUrl(),
									'document_url' => $this->active_document->getViewUrl(),
									'owner_company_name' => $owner_company->getName()
								), $this->active_document);
						} // if
					} // if
					
					db_commit();
					flash_success('Document ":document_name" has been created', array('document_name' => $this->active_document->getName()));
					$this->redirectToUrl($this->active_document->getViewUrl());
				} else {
					db_rollback();
					
					$this->smarty->assign('errors', $save);
				} // if
	    } // if
    } // add_text
    
    /**
     * Upload file document page action
     * 
     * @param void
     * @return void
     */
    function upload_file() {
	  	$this->wireframe->print_button = false;
	  	
	  	if(!Document::canAdd($this->logged_user)) {
		  	$this->httpError(HTTP_ERR_FORBIDDEN);
		  } // if
	  	
	  	$file = $_FILES['file'];
	  	$file_data = $this->request->post('file');
	  	
	  	if(!is_array($file_data)) {
	  		$file_data = array('category_id' => $this->active_document_category->getId());
	  	} // if
	  	
	  	require_once SMARTY_PATH . '/plugins/modifier.filesize.php';
	  	
	  	$this->smarty->assign(array(
	  		'file_data' => $file_data,
	  		'max_upload_size' => smarty_modifier_filesize(get_max_upload_size()),
	  	));
	  	
	  	if($this->request->isSubmitted()) {
	  		db_begin_work();
	  		$this->active_document->setAttributes($file_data);
	  		
	  		if(is_array($file)) {
	        $destination_file = get_available_uploads_filename();
	        if(move_uploaded_file($file['tmp_name'], $destination_file)) {
	          $this->active_document->setName($file['name']);
	          $this->active_document->setBody(basename($destination_file));
	          $this->active_document->setMimeType($file['type']);
	        } // if
		  	} // if
	  		$this->active_document->setCreatedBy($this->logged_user);
	  		$this->active_document->setType('file');
				$save = $this->active_document->save();
				
				if ($save && !is_error($save)) {
					$notify_user_ids = $this->request->post('notify_users');
					if(is_foreachable($notify_user_ids)) {
						$notify_users = Users::findByIds($notify_user_ids);
						$owner_company = get_owner_company();
						
						if(is_foreachable($notify_users)) {
								ApplicationMailer::send($notify_users, 'documents/new_upload_file_document', array(
									'document_name' => $this->active_document->getName(),
									'created_by_name' => $this->active_document->getCreatedByName(),
									'created_by_url' => $this->logged_user->getViewUrl(),
									'document_url' => $this->active_document->getViewUrl(),
									'owner_company_name' => $owner_company->getName()
								), $this->active_document);
						} // if
					} // if
					
					db_commit();
					flash_success('Document ":document_name" has been uploaded', array('document_name' => $this->active_document->getName()));
					$this->redirectTo('documents');
				} else {
					db_rollback();
					$this->smarty->assign('errors', $save);
				} // if
	  	} // if
    } // upload_file
    
    /**
     * Edit document page action
     *
     * @param void
     * @return void
     */
    function edit() {
    	$this->wireframe->print_button = false;
    	
    	if($this->active_document->isNew()) {
    		$this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_document->canEdit($this->logged_user)) {
    		$this->httpError(HTTP_ERR_FORBIDDEN);
    	} // if
    	
    	$document_data = $this->request->post('document');
    	if(!is_array($document_data)) {
    		$document_data = array(
    			'name' => $this->active_document->getName(),
    			'body' => $this->active_document->getBody(),
    			'category_id' => $this->active_document->getCategoryId(),
    			'visibility' => $this->active_document->getVisibility(),
    		);
    	} // if
    	
    	$this->smarty->assign('document_data', $document_data);

    	if($this->request->isSubmitted()) {
    		db_begin_work();
    		
    		$old_name = $this->active_document->getName();
    		
    		$this->active_document->setAttributes($document_data);
    		$save = $this->active_document->save();
    		
    		if($save && !is_error($save)) {
    			db_commit();
    			flash_success('Document ":document_name" has been updated', array ('document_name' => $old_name));
    			$this->redirectTo('documents');
    		} else {
    			db_rollback();
    			$this->smarty->assign('errors', $save);
    		} // if
    	} // if
    } // edit
    
    /**
     * Pin document
     *
     * @param void
     * @return void
     */
    function pin() {
    	if($this->active_document->isNew()) {
    		$this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_document->canPinUnpin($this->logged_user)) {
    		$this->httpError(HTTP_ERR_FORBIDDEN);
    	} // if
    	
    	if($this->request->isSubmitted()) {
    		db_begin_work();
    		$this->active_document->setIsPinned(1);
    		$save = $this->active_document->save();
    		
    		if($save && !is_error($save)) {
    			db_commit();
    			
    			if($this->request->isAsyncCall()) {
    				die($this->active_document->getUnpinUrl());
    			} else {
    				flash_success('Document ":document_name" has been pinned', array ('document_name' => $this->active_document->getName()));
    			} // if
    		} else {
    			db_rollback();
    			
    			if($this->request->isAsyncCall()) {
    				$this->httpError(HTTP_ERR_OPERATION_FAILED);
    			} else {
    				flash_error('Failed to pin document ":document_name"', array ('document_name' => $this->active_document->getName()));
    			} // if
    		} // if
    		$this->redirectToReferer(assemble_url('documents'));
    	} else {
    		$this->httpError(HTTP_ERR_BAD_REQUEST);
    	} // if
    } // pin
    
    /**
     * Unpin document
     *
     * @param void
     * @return void
     */
    function unpin() {
    	if($this->active_document->isNew()) {
    		$this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_document->canPinUnpin($this->logged_user)) {
    		$this->httpError(HTTP_ERR_FORBIDDEN);
    	} // if
    	
    	if($this->request->isSubmitted()) {
    		db_begin_work();
    		
    		$this->active_document->setIsPinned(0);
    		$save = $this->active_document->save();
    		
    		if($save && !is_error($save)) {
    			db_commit();
    			
    			if($this->request->isAsyncCall()) {
    				die($this->active_document->getPinUrl());
    			} else {
    				flash_success('Document ":document_name" has been unpinned', array ('document_name' => $this->active_document->getName()));
    			} // if
    		} else {
    			db_rollback();
    			
    			if($this->request->isAsyncCall()) {
    				$this->httpError(HTTP_ERR_OPERATION_FAILED);
    			} else {
    				flash_error('Failed to unpin document ":document_name"', array ('document_name' => $this->active_document->getName()));
    			} // if
    		} // if
    		$this->redirectToReferer(assemble_url('documents'));
    	} else {
    		$this->httpError(HTTP_ERR_BAD_REQUEST);
    	} // if
    } // unpin
    
    /**
     * Delete document action
     *
     * @param void
     * @return void
     */
    function delete() {
    	if($this->active_document->isNew()) {
    		$this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_document->canDelete($this->logged_user)) {
    		$this->httpError(HTTP_ERR_FORBIDDEN);
    	} // if
    	
    	if($this->request->isSubmitted()) {
    		db_begin_work();
    		$delete = $this->active_document->delete();
    		
    		if($delete && !is_error($delete)) {
    			db_commit();
    			flash_success('Document ":document_name" has been deleted', array('document_name' => $this->active_document->getName()));
    		} else {
    			db_rollback();
    			flash_error('Failed to delete ":document_name"', array('document_name' => $this->active_document->getName()));
    		} // if
    		$this->redirectTo('documents');
    	} else {
    		$this->httpError(HTTP_BAD_REQUEST);
    	} // if
    } // delete
    
  } // DocumentsController
  
?>