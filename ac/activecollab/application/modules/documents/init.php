<?php

  /**
   * Documents module initialization file
   * 
   * @package activeCollab.modules.documents
   */
  
  define('DOCUMENTS_MODULE', 'documents');
  define('DOCUMENTS_MODULE_PATH', APPLICATION_PATH . '/modules/documents');
  
  use_model(array('documents', 'document_categories'), DOCUMENTS_MODULE);
  
?>