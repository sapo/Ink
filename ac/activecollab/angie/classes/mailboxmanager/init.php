<?php

  /**
   * mailboxmanager initialization file
   * 
   * @package angie.library.mailboxmanager
   */
  
  define('MAILBOX_MANAGER_LIB_PATH', ANGIE_PATH . '/classes/mailboxmanager');
  
  /**
   * Constants
   */ 
  define('CAN_USE_MAILBOX_MANAGER', extension_loaded('php_imap'));
  
  if (!function_exists('imap_savebody') || FAIL_SAFE_IMAP_FUNCTIONS) {
    define('MM_CAN_DOWNLOAD_LARGE_ATTACHMENTS', false);  
  } else {
    define('MM_CAN_DOWNLOAD_LARGE_ATTACHMENTS', true);
  } // if
  
  // server types
  define('MM_SERVER_TYPE_POP3', 'POP3');
  define('MM_SERVER_TYPE_IMAP', 'IMAP');
  
  // server security
  define('MM_SECURITY_NONE','NONE');
  define('MM_SECURITY_TLS', 'TLS');
  define('MM_SECURITY_SSL', 'SSL');
  
  define('MM_DEFAULT_MAILBOX', 'INBOX');
  
  // email clients
  define('MM_EMAIL_CLIENT_APPLE_MAIL','Apple Mail');
  define('MM_EMAIL_CLIENT_GMAIL', 'Gmail');
  
  // functions
  require_once(MAILBOX_MANAGER_LIB_PATH.'/functions.php');
  
  // classes
  require_once(MAILBOX_MANAGER_LIB_PATH.'/MailboxManagerEmail.class.php');
  require_once(MAILBOX_MANAGER_LIB_PATH.'/PHPImapMailboxManager.class.php');
  
  require_once ANGIE_PATH . '/classes/htmlpurifier/init.php';
  
?>