<?php

  /**
   * IncomingMailActivityLog class
   */
  class IncomingMailActivityLog extends BaseIncomingMailActivityLog {
    
    /**
     * Mailbox cache
     *
     * @var unknown_type
     */
    var $mailbox = null;
  
    /**
     * Returns Mailbox which is associated with object
     *
     * @param void
     * @return IncomingMailbox
     */
    function getMailbox() {
      if ($this->mailbox === null) {
        $this->mailbox = IncomingMailboxes::findById($this->getMailboxId());
      } // if
      return $this->mailbox;
    } // getMailbox
    
    /**
     * Return mailbox name
     *
     * @return string
     */
    function getMailboxDisplayName() {
      $mailbox = $this->getMailbox();
      if (instance_of($mailbox, 'IncomingMailbox')) {
        return $this->mailbox->getDisplayName();
      } // if
      return lang('Unknown');
    } // getMailboxName
    
    /**
     * Return mailbox view URL
     *
     * @return string
     */
    function getMailboxViewUrl() {
      $mailbox = $this->getMailbox();
      if (instance_of($mailbox, 'IncomingMailbox')) {
        return $this->mailbox->getViewUrl();
      } // if
      return lang('Unknown');
    } // getMailboxViewUrl
    
    /**
     * Get view url for resulting object
     *
     * @return string
     */
    function getResultingObjectUrl() {
      if ($this->getProjectObjectId()) {
        $object = ProjectObjects::findById($this->getProjectObjectId());
        if (instance_of($object, 'ProjectObject')) {
          return $object->getViewUrl();
        } // if
      } else if ($this->getIncomingMailId()) {
        $object = IncomingMails::findById($this->getIncomingMailId());
        if (instance_of($object, 'IncomingMail')) {
          return $object->getImportUrl();
        } // if
      } // if
      return false;
    } // getCreatedObjectUrl()
  } // IncomingMailActivityLog

?>