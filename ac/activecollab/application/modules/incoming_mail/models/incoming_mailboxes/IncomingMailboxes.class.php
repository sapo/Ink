<?php

  /**
   * IncomingMailboxes class
   */
  class IncomingMailboxes extends BaseIncomingMailboxes {
  
    /**
     * Find all active mailboxes
     *
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    function findAllActive($offset = null, $limit = null) {
      return IncomingMailboxes::find(array(
        'conditions' => array('enabled > 0'),
        'order'      => 'id DESC',
        'offset'     => $offset,
        'limit'      => $limit,
      ));
    } // findAllActive
    
    /**
     * Find mailbox by from_email field
     *
     * @param string $email
     * @return IncomingMailbox
     */
    function findByFromEmail($email) {
      return IncomingMailboxes::find(array(
        'conditions'  => array('from_email = ?', $email),
        'limit'       => 1,
        'one'         => true,
      ));
    } // findByFromEmail
  
  }

?>