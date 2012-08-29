<?php

  /**
   * DocumentCategories class
   * 
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class DocumentCategories extends BaseDocumentCategories {
    
    /**
     * Return categories that have documents $user can see
     * 
     * Only if $user is administrator or can see private objects all categories
     * are returned
     *
     * @param User $user
     * @return array
     */
    function findAll($user) {
      if($user->isAdministrator() || $user->canSeePrivate()) {
        return DocumentCategories::find(array(
          'order' => 'name',
        ));
      } else {
        $document_categories_table = TABLE_PREFIX . 'document_categories';
        $documents_table = TABLE_PREFIX . 'documents';
        
        return DocumentCategories::findBySQL("SELECT DISTINCT $document_categories_table.* FROM $document_categories_table, $documents_table WHERE $document_categories_table.id = $documents_table.category_id AND $documents_table.visibility >= ? ORDER BY $document_categories_table.name", array(VISIBILITY_NORMAL));
      } // if
    } // findAll
  
  }

?>