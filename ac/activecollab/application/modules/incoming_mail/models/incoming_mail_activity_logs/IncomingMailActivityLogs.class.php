<?php

  /**
   * IncomingMailActivityLogs class
   */
  class IncomingMailActivityLogs extends BaseIncomingMailActivityLogs {
  
    /**
     * Find mailbox activity history
     *
     * @param IncomingMailbox $mailbox
     */
    function findByMailbox($mailbox) {
      return parent::find(array(
          'conditions' => array('mailbox_id = ?', $mailbox->getId()),
          'order' => 'created_on DESC'
      ));
    } // findByMailbox
    
    /**
     * Paginate mailbox activity history
     *
     * @param IncomingMailbox $mailbox
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateByMailbox($mailbox, $page = 1, $per_page = 30) {
      return parent::paginate(array(
        'conditions' =>  array('mailbox_id = ?', $mailbox->getId()),
        'order' => 'created_on DESC'
      ), $page, $per_page);
    } // paginateByMailbox
    
    /**
     * Paginate only conflicted activity history by mailbox
     *
     * @param IncomingMailbox $mailbox
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateConflictsByMailbox($mailbox, $page = 1, $per_page = 30) {
      return parent::paginate(array(
        'conditions' =>  array('mailbox_id = ? AND state = 0', $mailbox->getId()),
        'order' => 'created_on DESC'
      ), $page, $per_page);      
    } // paginateImportantByMailbox
    
    /**
     * Paginate conflicted activity history
     *
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    function paginateConflicts($page = 1, $per_page = 30) {
      return parent::paginate(array(
        'conditions' => 'state = 0',
        'order' => 'created_on DESC'   
      ), $page, $per_page);
    } // paginateImportant
    
    /**
     * Log mail event
     *
     * @param integer $mailbox_id
     * @param string $message
     * @param mixed $object 
     * @param integer $status
     * @param DateTimeValue $date
     */
    function log($mailbox_id, $message, $object, $status, $date = null) {
      if (!$date) {
        $date = new DateTimeValue();
      } // if
      
      $log = new IncomingMailActivityLog();
      $log->setResponse($message);
      $log->setState($status);
      $log->setMailboxId($mailbox_id);
      $log->setCreatedOn($date);

      if (instance_of($object, 'IncomingMail')) {
        $log->setIncomingMailId($object->getId());
        $log->setSubject($object->getSubject());
        $log->setSender($object->getCreatedByEmail());
      } else if (instance_of($object, 'ProjectObject')) {
        $log->setProjectObjectId($object->getId());
        $log->setSubject($object->getName());
        $log->setSender($object->getCreatedByEmail());
      } if (instance_of($object, 'MailboxManagerEmail')) {
        $log->setSubject($object->getSubject());
        $sender = $object->getAddress('from');
        $log->setSender(array_var($sender,'email'));
      } // if
      
      return $log->save();
    } // log
  
  } // IncomingMailActivityLogs

?>