<?php

/**
 * Base class for mailboxes
 * 
 * @package angie.classes.mailboxmanager
 */

  class MailboxManager extends AngieObject {
    /**
     * Mailbox connection
     * 
     * @var mixed
     */
    var $connection;
    
    /**
     * Tells if manager is connected
     * 
     * @var boolean
     */
    var $connected = false;
    
    /**
     * Mailbox parameters
     * 
     * @var array
     */
    var $mailbox_parameters;
    
    /**
     * User credentials
     *
     * @var array
     */
    var $mailbox_user;
    
    /**
     * Errors
     * 
     * @var array
     */
    var $errors;
    
    /**
     * Construct MailboxManager class
     *
     * @param string $server_address
     * @param string $server_type
     * @param string $server_security
     * @param string $server_port
     * @param string $mailbox_name
     * @param string $username
     * @param string $password
     */
    function __construct($server_address=null, $server_type=null, $server_security=null, $server_port=null, $mailbox_name=null, $username=null, $password=null) {
      $this->mailbox_parameters = array(
        'server_address'  => $server_address,
        'server_type'     => $server_type,
        'server_security' => $server_security ? $server_security : MM_SECURITY_NONE,
        'server_port'     => $server_port ? $server_port : mm_get_default_port($server_security),
        'mailbox_name'    => $mailbox_name
      );
      
      $this->mailbox_user = array(
        'username' => $username,
        'password' => $password
      );
      
      return $this;
    } // __construct

    /**
     * Returns server address
     * 
     * @param void
     * @return string
     */
    function getServerAddress() {
      return array_var($this->mailbox_parameters,'server_address', null);
    } // getServerAddress
    
    /**
     * Returns server type
     * 
     * @param void
     * @return string
     */
    function getServerType() {
      return array_var($this->mailbox_parameters,'server_type', null);
    } // getServerAddress
    
    /**
     * Returns server security
     * 
     * @param void
     * @return string
     */
    function getServerSecurity() {
      return array_var($this->mailbox_parameters,'server_security', null);
    } // getServerSecurity
    
    /**
     * Returns server port
     * 
     * @param void
     * @return string
     */
    function getServerPort() {
      return array_var($this->mailbox_parameters,'server_port', null);
    } // getServerPort
    
    /**
     * Returns mailbox name
     * 
     * @param void
     * @return string
     */
    function getMailboxName() {
      return array_var($this->mailbox_parameters,'mailbox_name', null);
    } // getMailboxName
    
    /**
     * Returns mailbox username
     *
     * @param void
     * @return string
     */
    function getMailboxUsername() {
      return array_var($this->mailbox_user, 'username', null);
    } // getMailboxUsername
    
    /**
     * Returns mailbox password
     *
     * @param void
     * @return string
     */
    function getMailboxPassword() {
      return array_var($this->mailbox_user, 'password', null);
    } // getMailboxPassword
    
    /**
     * Connect method
     * 
     * @param void
     * @return boolean
     */
    function connect() {
      if (!$this->getServerAddress()) {
        return new Error(lang('Server address is not defined'));
      } // if
      
      $port = $this->getServerPort();
      if ($port<1 || $port>65535 || !trim($port)) {
        return new Error(lang('Server port needs to be in range 1-65535'));
      } // if
      
      return false;
    } // connect
        
    /**
     * Disconnect from server
     * 
     * @param void
     * @return boolean
     */
    function disconnect() {
      $this->connected = false;
      return true;
    } // disconnect
    
    /**
     * Test connection. If it's ok then it returns true, otherwise returns connection error
     * 
     * @param void
     * @return boolean
     */
    function testConnection() {
      if ($this->connected == true) {
        return true;
      } // if

      $test_connection = $this->connect();
      if (!is_error($test_connection)) {
        $this->disconnect();
        return true;
      };
      return $test_connection;
    } // testConnection
    
   /**
    * Assembles pop3/imap connection string
    *
    * @param boolean $novalidate_cert
    * @return string
    */
    function getConnectionString($novalidate_cert = true) {
     $connection_str = '{' . $this->getServerAddress() . ':' . $this->getServerPort() . '/' . $this->getServerType();
      
      if ($this->getServerSecurity() != MM_SECURITY_NONE) {
        $connection_str.= '/'.$this->getServerSecurity();
        if ($novalidate_cert) {
          $connection_str.= '/novalidate-cert';
        } else {
        	$connection_str.= '/validate-cert';
        }
      } else {
        $connection_str.= '/notls';
      } // if
    
      $connection_str.= '}'.$this->getMailboxName();
      return $connection_str;
    } // getConnectionString
      
    /**
     * Return connection
     * 
     * @param void
     * @return mixed
     */
    function getConnection() {
      return $this->connection;
    } // getConnection
    
    /**
     * Check if connected
     *
     * @return boolean
     */
    function isConnected() {
      return (boolean) $this->connection;
    } // isConnected
    
    /**
     * If is connected returns true if not, raises exception
     * 
     * @param void
     * @return boolean
     */
    function requireConnection() {
      if (!$this->isConnected()) {
        return new Error(lang("You are not connected, can't execute a command"), true);
      };
      return true;
    }
    
    /**
     * Get Number of messages in mailbox
     * 
     * @param void
     * @return integer
     */
    function countMessages() {
      $this->requireConnection();
      return 0;
    } // countMessages
    
    /**
     * Get Number of unread messages
     * 
     * @param void
     * @return integer
     */
    function countUnreadMessages() {
      $this->requireConnection();
      return 0;
    } // countUnreadMessages
    
    /**
     * Retrieves message headers
     *
     * @param mixed $message_id
     * @return mixed
     */
    function getMessageHeaders($message_id) {
      $this->requireConnection();
      return false;
    }  // getMessageHeaders
    
    /**
     * Delete messsage
     *
     * @param mixed $message_id
     */
    function deleteMessage($message_id) {
      $this->requireConnection();
      return false;
    } // deleteMessage
    

    /**
     * Retrieves headers for multiple emails
     *
     * @param array $message_ids
     */
    function getHeaders($message_ids) {
      $headers = array();
      if (is_foreachable($message_ids)) {
        foreach ($message_ids as $message_id) {
        	$headers[] = $this->getMessageHeaders($message_id);
        } // foreach
      } // if
      return $headers;
    } // getHeaders
    
    
    /**
     * Return message with id $message_id
     *
     * @param int $message_id
     * @return MailboxManagerEmail
     */
    function getMessage($message_id) {
      return null;  
    } // getMessage
  
  } // MailboxManager

?>