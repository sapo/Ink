<?php

  /**
   * Documents class
   * 
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class Documents extends BaseDocuments {
    
    /**
     * Return all documents sorted by added date
     *
     * @param void
     * @return array
     */
    function findAll() {
      return Documents::find(array(
        'order' => 'created_on DESC',
      ));
    } // findAll
  	
  	/**
     * Return all documents that belong to a category
     *
     * @param DocumentCategory $category
     * @return array
     */
    function findByCategory($category) {
      return Documents::find(array(
        'conditions' => array('category_id = ?', $category->getId()),
        'order' => 'name'
      ));
    } // findByCategory
  
    /**
     * Paginate all documents
     *
     * @param integer $min_visibility
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateDocuments($min_visibility = VISIBILITY_PRIVATE, $page = 1, $per_page = 30) {
      return Documents::paginate(array(
      	'conditions' => array('visibility >= ?', $min_visibility),
        'order' => 'is_pinned DESC, name',
      ), $page, $per_page);
    } // paginateDocuments
    
    /**
     * Paginate documents by category
     *
     * @param DocumentCategory $category
     * @param integer	$min_visibility
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateByCategory($category, $min_visibility = VISIBILITY_PRIVATE, $page = 1, $per_page = 30) {
      return Documents::paginate(array(
        'conditions' => array('category_id = ? AND visibility >= ?', $category->getId(), $min_visibility),
        'order' => 'is_pinned DESC, name',
      ), $page, $per_page);
    } // paginateByCategory
  
  }

?>