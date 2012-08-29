<?php
  /**
   * Backup module on_daily event handler
   *
   * @package activeCollab.modules.incoming_mail
   * @subpackage handlers
   */

  /**
   * do daily cleanup script
   *
   * @param null
   * @return null
   */
  function incoming_mail_handle_on_daily() {
    // remove activity log entries that are older than 30 days
    IncomingMailActivityLogs::delete(array('created_on < ?', new DateTimeValue('-30 days')));
  } // incoming_mail_handle_on_hourly
?>