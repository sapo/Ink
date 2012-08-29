<?php

  /**
   * IncomingMailbox class
   */
  class IncomingMailbox extends BaseIncomingMailbox {
    
    /**
     * Project that is tided to this object
     */
    var $project = false;
  
    /**
     * Function to validate class
     *
     * @param ValidationErrors $errors
     */
    function validate(&$errors) {
      if(!$this->validatePresenceOf('project_id')) {
        $errors->addError(lang('required'), 'project_id');
      } // if
      
      if(!$this->validatePresenceOf('object_type')) {
        $errors->addError(lang('required'), 'object_type');
      } // if
      
      if(!$this->validatePresenceOf('mailbox')) {
        $errors->addError(lang('required'), 'mailbox');
      } // if
      
      if(!$this->validatePresenceOf('username')) {
        $errors->addError(lang('required'), 'username');
      } // if
      
      if(!$this->validatePresenceOf('password')) {
        $errors->addError(lang('required'), 'password');
      } // if
      
      if(!$this->validatePresenceOf('host')) {
        $errors->addError(lang('required'), 'host');
      } // if
      
      if(!$this->validatePresenceOf('type')) {
        $errors->addError(lang('required'), 'type');
      } // if
      
      if(!$this->validatePresenceOf('security')) {
        $errors->addError(lang('required'), 'security');
      } // if
      
      if(!$this->validatePresenceOf('from_email')) {
        $errors->addError(lang('required'), 'from_email');
      } else {
        // validate that user does not exists
        $current_user = Users::findByEmail($this->getFromEmail());
        if (instance_of($current_user, 'User')) {
          $errors->addError(lang('email in use'), 'from_email');
        } // if
      } // if

      parent::validate($errors, true);
    } // validate
    
    /**
     * Returns string dependable of last status check
     *
     * @return string
     */
    function getFormattedLastStatus() {
      switch ($this->getLastStatus()) {          
        case 1:
          return lang('Status OK');
          break;
          
        case 2:
          return lang('Last update failed');
          break;
        
        default:
          return lang('Not Checked');
          break;
      } // switch
    } // getFormattedLastStatus
    
    /**
     * Returns project
     *
     * @return Project
     */
    function getProject() {
      if ($this->project === false) {
        $this->project = Projects::findById($this->getProjectId());
      } // if
      return $this->project;
    } // getProject
    
    /**
     * Returns Project Name
     * 
     * @return string
     */
    function getProjectName() {
     $project = $this->getProject();
    	return instance_of($project, 'Project') ? $project->getName() : lang('-- Unknown --');
    } // getProjectName
    
    // URLS
    
    /**
     * Return view Mailbox URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
      return assemble_url('incoming_mail_admin_view_mailbox', array(
        'mailbox_id' => $this->getId()
      ));
    } // getViewUrl
    
    /**
     * Return list emails Mailbox URL
     *
     * @param void
     * @return string
     */
    function getListEmailsUrl() {
      return assemble_url('incoming_mail_admin_mailbox_list_messages', array(
        'mailbox_id' => $this->getId()
      ));
    } // getViewUrl
    
    /**
     * Return edit mailbox URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return assemble_url('incoming_mail_admin_edit_mailbox', array(
        'mailbox_id' => $this->getId()
      ));
    } // getEditUrl
    
    /**
     * Return delete mailbox URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return assemble_url('incoming_mail_admin_delete_mailbox', array(
        'mailbox_id' => $this->getId()
      ));      
    } // getTrashUrl
    
    /**
     * Returns manager object
     * 
     * @param void
     * @return PHPImapMailboxManager
     */
    function getMailboxManager () {
      return new PHPImapMailboxManager(
        $this->getHost(),
        $this->getType(),
        $this->getSecurity(),
        $this->getPort(),
        $this->getMailbox(),
        $this->getUsername(),
        $this->getPassword()
      );
    } // getMailboxManager
    
    /**
     * get mailbox display name
     *
     * @return string
     */
    function getDisplayName() {
      if ($this->getFromName()) {
        return $this->getFromName();
      } // if
      
      if ($this->getFromName()) {
        return $this->getFromName();
      } // if
      
      return $this->getHost();
    } // getDisplayName
  
  } // IncomingMailbox

?>