<?php
  /**
   * Backup module on_frequently event handler
   *
   * @package activeCollab.modules.incoming_mail
   * @subpackage handlers
   */

  /**
   * do frequently backup
   *
   * @param null
   * @return null
   */
  function incoming_mail_handle_on_frequently() {
      set_time_limit(0);
      require_once(INCOMING_MAIL_MODULE_PATH.'/models/IncomingMailImporter.class.php');
      require_once ANGIE_PATH . '/classes/UTF8Converter/init.php';
      require_once ANGIE_PATH . '/classes/mailboxmanager/init.php';

      $mailboxes = IncomingMailboxes::findAllActive();     
      IncomingMailImporter::importEmails($mailboxes, 20);
  } // incoming_mail_handle_on_hourly
?>