<?php

  // We need ApplicationController
  use_controller('application', SYSTEM_MODULE);

  /**
   * Document Categories controller
   *
   * @package activeCollab.modules.documents
   * @subpackage controllers
   */
  class DocumentCategoriesController extends ApplicationController {
    
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
    var $controller_name = 'document_categories';
    
    /**
     * Selected Document Category
     *
     * @var DocumentCategory
     */
    var $active_document_category;
    
    /**
     * Constructor method
     *
     * @param string $request
     * @return DocumentsCategoriesController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if(!$this->logged_user->isAdministrator() && !$this->logged_user->getSystemPermission('can_use_documents')) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->wireframe->addBreadCrumb(lang('Documents'), assemble_url('documents'));
      
      $category_id = $this->request->getId('category_id');
      if ($category_id) {
      	$this->active_document_category = DocumentCategories::findById($category_id);
      } // if
      
      if(instance_of($this->active_document_category, 'DocumentCategory')) {
        $this->wireframe->addBreadCrumb($this->active_document_category->getName(), $this->active_document_category->getViewUrl());
        
        if(Document::canAdd($this->logged_user)) {
          $add_text_url = assemble_url('documents_add_text', array('category_id' => $this->active_document_category->getId()));
  	      $upload_file_url = assemble_url('documents_upload_file', array('category_id' => $this->active_document_category->getId()));
        } // if
      } else {
      	$this->active_document_category = new DocumentCategory();
      	
      	if(Document::canAdd($this->logged_user)) {
  	      $add_text_url = assemble_url('documents_add_text');
  	      $upload_file_url = assemble_url('documents_upload_file');
      	} // if
      } // if
      
      if(Document::canAdd($this->logged_user)) {
	      $this->wireframe->addPageAction(lang('New Text Document'), $add_text_url);
		    $this->wireframe->addPageAction(lang('Upload File'), $upload_file_url);
      } else {
        $add_text_url = null;
        $upload_file_url = null;
      } // if
      
      $this->smarty->assign(array(
      	'document_categories_url' => $this->logged_user->isAdministrator() ? assemble_url('document_categories') : null,
      	'add_category_url' => DocumentCategory::canAdd($this->logged_user) ? assemble_url('document_categories_add') : null,
      	'add_text_url' => $add_text_url,
      	'upload_file_url' => $upload_file_url,
      	'active_document_category' => $this->active_document_category,
      	'categories' => DocumentCategories::findAll($this->logged_user),
      ));
    } // __construct
    
    /**
     * Index page action
     * 
     * @param void
     * @return void
     */
    function index() {
      if(!$this->logged_user->isAdministrator()) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
    } // index
    
    /**
     * View documents category page action
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_document_category->isNew()) {
    		$this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_document_category->canView($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->setTemplate(array(
        'template' => 'index',
        'controller' => 'documents',
        'module' => DOCUMENTS_MODULE,
      ));
    	
    	$page = (integer) $this->request->get('page');
    	if($page < 1) {
    	  $page = 1;
    	} // if
    	
    	$per_page = 10;
    	list($documents, $pagination) = Documents::paginateByCategory($this->active_document_category, $this->logged_user->getVisibility(), $page, $per_page);

    	$this->smarty->assign(array(
    	  'documents' => $documents,
    	  'pagination' => $pagination,
    	));
    	
    	js_assign(array(
    		'pin_icon_url' => get_image_url('icons/pinned.16x16.gif'),
    		'unpin_icon_url' => get_image_url('icons/not-pinned.16x16.gif')
    	));
    } // view
    
    /**
     * Create a new category page action
     *
     * @param void
     * @return null
     */
    function add() {
    	$this->wireframe->print_button = false;
    	
    	if(!DocumentCategory::canAdd($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
	  	
	  	$category_data = $this->request->post('category');
	  	
	  	$this->smarty->assign('category_data', $category_data);

	  	if ($this->request->isSubmitted()) {
	  		db_begin_work();
	  		$this->active_document_category->setAttributes($category_data);
	  		$save = $this->active_document_category->save();
	  		
	  		if($save && !is_error($save)) {
	  			db_commit();
	  			
	  			if($this->request->isAsyncCall()) {
	  			  $this->smarty->assign('document_category', $this->active_document_category);
            print $this->smarty->fetch(get_template_path('_document_category_row', 'document_categories', DOCUMENTS_MODULE));
            die();
	  			} else {
	  			  flash_success('Category ":name" has been created', array('name' => $this->active_document_category->getName()));
  	  			$this->redirectTo('document_categories');
	  			} // if
	  		} else {
	  			db_rollback();
	  			if($this->request->isAsyncCall()) {
	  			  $this->serveData($save);
	  			} else {
	  			  $this->smarty->assign('errors', $save);
	  			} // if
	  		} // if
	  	} // if
    } // add
    
    /**
     * Quick add document category
     *
     * @param void
     * @return null
     */
    function quick_add() {
      if($this->request->isSubmitted() && $this->request->isAsyncCall()) {
        if(!DocumentCategory::canAdd($this->logged_user)) {
          $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
        } // if
        
        $document_category = new DocumentCategory();
        $document_category->setAttributes($this->request->post('document_category'));
        
        $save = $document_category->save();
        if($save && !is_error($save)) {
          print $document_category->getId();
          die();
        } else {
          $this->serveData($save);
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // quick_add
    
    /**
     * Edit document category page action
     * 
     * @param void
     * @return void
     */
    function edit() {
    	$this->wireframe->print_button = false;
    	
    	if ($this->active_document_category->isNew()) {
    		$this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_document_category->canEdit($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
    	
    	$category_data = $this->request->post('category');
    	if (!is_array($category_data)) {
    	  $category_data = array('name' => $this->active_document_category->getName());
    	} // if
    	$this->smarty->assign('category_data', $category_data);
    	
    	if ($this->request->isSubmitted()) {
    	  db_begin_work();
    	  
    		$old_name = $this->active_document_category->getName();
    		$this->active_document_category->setAttributes($category_data);
    		
    		$save = $this->active_document_category->save();
    		if($save && !is_error($save)) {
    		  db_commit();
    		  
    		  if($this->request->isAsyncCall()) {
    		    $this->renderText($this->active_document_category->getName());
    		  } else {
    		    flash_success('Category ":category_name" has been updated', array('category_name' => $old_name));
      			$this->redirectTo('document_categories');
    		  } // if
    		} else {
    		  db_rollback();
    		  if($this->request->isAsyncCall()) {
    		    $this->serveData($save);
    		  } else {
    			  $this->smarty->assign('errors', $save);
    		  } // if
    		} // if
    	} // if
    } // edit
    
    /**
     * Delete document category
     *
     * @param void
     * @return void
     */
    function delete() {
    	if($this->active_document_category->isNew()) {
    		$this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_document_category->canDelete($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
    	
    	if ($this->request->isSubmitted()) {
    	  db_begin_work();
    		$delete = $this->active_document_category->delete();
    		
    		if($delete && !is_error($delete)) {
    		  db_commit();
    		  
    		  if($this->request->isApiCall()) {
    		    $this->httpOk();
    		  } else {
    		    flash_success('Document category ":name" has been deleted', array('name' => $this->active_document_category->getName()));
    		    $this->redirectTo('document_categories');
    		  } // if
    		} else {
    		  db_rollback();
    		  
    		  if($this->request->isAsyncCall()) {
    		    $this->serveData($delete);
    		  } else {
    		    flash_success('Failed to delete ":name" document category', array('name' => $this->active_document_category->getName()));
    		    $this->redirectTo('document_categories');
    		  } // if
    		} // if
    	} else {
    		$this->httpError(HTTP_ERR_BAD_REQUEST);
    	} // if
    } // delete
    
  }
  
?>