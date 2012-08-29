<?php

/**
 * Swift Mailer Logging Layer base class.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Log
 * @license GNU Lesser General Public License
 */

if (!defined("SWIFT_LOG_COMMAND")) define("SWIFT_LOG_COMMAND", ">>");
if (!defined("SWIFT_LOG_RESPONSE")) define("SWIFT_LOG_RESPONSE", "<<");
if (!defined("SWIFT_LOG_ERROR")) define("SWIFT_LOG_ERROR", "!!");
if (!defined("SWIFT_LOG_NORMAL")) define("SWIFT_LOG_NORMAL", "++");
if (!defined("SWIFT_LOG_NOTHING")) define("SWIFT_LOG_NOTHING", 0);
if (!defined("SWIFT_LOG_ERRORS")) define("SWIFT_LOG_ERRORS", 1);
if (!defined("SWIFT_LOG_FAILURES")) define("SWIFT_LOG_FAILURES", 2);
if (!defined("SWIFT_LOG_NETWORK")) define("SWIFT_LOG_NETWORK", 3);
if (!defined("SWIFT_LOG_EVERYTHING")) define("SWIFT_LOG_EVERYTHING", 4);

/**
 * The Logger class/interface.
 * @package Swift_Log
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Log
{
  /**
   * A command type entry
   */
  var $COMMAND = ">>";
  /**
   * A response type entry
   */
  var $RESPONSE = "<<";
  /**
   * An error type entry
   */
  var $ERROR = "!!";
  /**
   * A standard entry
   */
  var $NORMAL = "++";
  /**
   * Logging is off.
   */
  var $LOG_NOTHING = 0;
  /**
   * Only errors are logged.
   */
  var $LOG_ERRORS = 1;
  /**
   * Errors + sending failures.
   */
  var $LOG_FAILURES = 2;
  /**
   * All SMTP instructions + failures + errors.
   */
  var $LOG_NETWORK = 3;
  /**
   * Runtime info + SMTP instructions + failures + errors.
   */
  var $LOG_EVERYTHING = 4;
  /**
   * Failed recipients
   * @var array
   */
  var $failedRecipients = array();
  /**
   * The maximum number of log entries
   * @var int
   */
  var $maxSize = 50;
  /**
   * The level of logging currently set.
   * @var int
   */
  var $logLevel = SWIFT_LOG_NOTHING;
  
  /**
   * Add a new entry to the log
   * @param string The information to log
   * @param string The type of entry (see the constants: COMMAND, RESPONSE, ERROR, NORMAL)
   */
  function add($text, $type = SWIFT_LOG_NORMAL) {}
  /**
   * Dump the contents of the log to the browser.
   * @param boolean True if the string should be returned rather than output.
   */
  function dump($return_only=false) {}
  /**
   * Empty the log contents
   */
  function clear() {}
  /**
   * Check if logging is enabled.
   */
  function isEnabled()
  {
    return ($this->logLevel > $this->LOG_NOTHING);
  }
  /**
   * Add a failed recipient to the list
   * @param string The address of the recipient
   */
  function addFailedRecipient($address)
  {
    $this->failedRecipients[$address] = null;
    $this->add("Recipient '" . $address . "' rejected by connection.", $this->ERROR);
  }
  /**
   * Get the list of failed recipients
   * @return array
   */
  function getFailedRecipients()
  {
    return array_keys($this->failedRecipients);
  }
  /**
   * Set the maximum size of this log (zero is no limit)
   * @param int The maximum entries
   */
  function setMaxSize($size)
  {
    $this->maxSize = (int) $size;
  }
  /**
   * Get the current maximum allowed log size
   * @return int
   */
  function getMaxSize()
  {
    return $this->maxSize;
  }
  /**
   * Set the log level to one of the constants provided.
   * @param int Level
   */
  function setLogLevel($level)
  {
    $level = (int)$level;
    $this->add("Log level changed to " . $level, $this->NORMAL);
    $this->logLevel = $level;
  }
  /**
   * Get the current log level.
   * @return int
   */
  function getLogLevel()
  {
    return $this->logLevel;
  }
  /**
   * Check if the log level includes the one given.
   * @param int Level
   * @return boolean
   */
  function hasLevel($level)
  {
    return ($this->logLevel >= ((int)$level));
  }
}
