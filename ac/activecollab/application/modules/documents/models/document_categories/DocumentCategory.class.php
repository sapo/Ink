<?php

  /**
   * DocumentCategory class
   * 
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class DocumentCategory extends BaseDocumentCategory {
    
    /**
     * Cached category documents
     *
     * @var array
     */
    var $documents = false;
  	
  	/**
     * Return all documents that belong to a category
     *
     * @param void
     * @return array
     */
    function getDocuments() {
      if($this->documents === false) {
        $this->documents = Documents::findByCategory($this);
      } // if
      return $this->documents;
    } // getDocuments
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can add new category
     *
     * @param User $user
     * @return boolean
     */
    function canAdd($user) {
      return $user->isAdministrator();
    } // canAdd
    
    /**
     * Returns true if $user can view specific document category
     *
     * @param User $user
     * @return boolean
     */
    function canView($user) {
      return true;
    } // canView
    
    /**
     * Return true if $user can edit this category
     *
     * @param User $user
     * @return boolean
     */
    function canEdit($user) {
      return $user->isAdministrator();
    } // canEdit
    
    /**
     * Returns true if $user can delete this category
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      return $user->isAdministrator();
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
  
    /**
     * Return view Document Category URL
     *
     * @param integer $page
     * @return string
     */
    function getViewUrl($page = null) {
      $params = array('category_id' => $this->getId());
      
      if($page !== null) {
        $params['page'] = $page;
      } // if
      
      return assemble_url('document_category_view', $params);
    } // getViewUrl
    
    /**
     * Return edit Document Category URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('document_category_edit', array(
        'category_id' => $this->getId()
      ));
    } // getEditUrl
    
    /**
     * Return delete Document Category URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('document_category_delete', array(
        'category_id' => $this->getId()
      ));
    } // getDeleteUrl
    
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
    	if($this->validatePresenceOf('name', 3)) {
    	  if(!$this->validateUniquenessOf('name')) {
    	    $errors->addError(lang('Category name needs to be unique'), 'name');
    	  } // if
    	} else {
    		$errors->addError(lang('Category name must be at least 3 characters long'), 'name');
    	} // if
    	
    	parent::validate($errors, true);
    } // validate
    
    /**
     * Drop this category
     *
     * @param void
     * @return boolean
     */
    function delete() {
      db_begin_work();
      
      $delete = parent::delete();
      if($delete && !is_error($delete)) {
        $documents = $this->getDocuments();
        foreach($documents as $document) {
          $document->delete();
        } // foreach
        
        db_commit();
        return true;
      } else {
        db_rollback();
        return $delete;
      } // if
    } // delete
  
  }

?>