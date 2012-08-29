<?php

  /**
   * IncomingMail class
   */
  class IncomingMail extends BaseIncomingMail {
    
    /**
     * Returns project
     * 
     * @return Project
     */
    function getProject() {
      return Projects::findById($this->getProjectId());
    } // getProject
    
    /**
     * Retrieve all attachments
     * 
     * @return array
     *
     */
    function getAttachments() {
      return IncomingMailAttachments::find(array(
        "conditions" => array('`mail_id` = ?', $this->getId()),
      ));
    } // getAttachments
    
    /**
     * Delete object
     * 
     * @return boolean
     */
    function delete() {
      $attachments = $this->getAttachments();
      
      if (is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
        	$attachment->delete();
        } // foreach
      } // if
      
      return parent::delete();
    } // delete
    
    /**
     * Return edit mail URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('incoming_mail_edit_mail', array(
        'mail_id' => $this->getId()
      ));
    } // getEditUrl
    
    /**
     * Return delete mailbox URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('incoming_mail_delete_mail', array(
        'mail_id' => $this->getId(),
      ));      
    } // getDeleteUrl
    
    /**
     * Return import mailbox URL
     *
     * @param void
     * @return string
     */
    function getImportUrl() {
      return assemble_url('incoming_mail_import_mail', array(
        'mail_id' => $this->getId(),
      ));
    } // getImportUrl
  } // incomingMail

?>