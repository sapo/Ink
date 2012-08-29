<?php

/**
 * Swift mailer default logger
 * @package Swift_Log
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Log");

/**
 * Angie logger
 * @package Swift_Log
 */
class Swift_Log_AngieLog extends Swift_Log {
  
  /**
   * Add a log entry
   * @param string The text for this entry
   * @param string The label for the type of entry
   */
  function add($text, $type = SWIFT_LOG_NORMAL) {
    log_message($text, LOG_LEVEL_INFO, 'mailing');
  }
  /**
   * Dump the contents of the log to the browser.
   * @param boolean True if the string should be returned rather than output.
   */
  function dump($return_only=false) {
    return '';
  }
  /**
   * Empty the log
   */
  function clear() {
    $this->failedRecipients = array();
  }
}
