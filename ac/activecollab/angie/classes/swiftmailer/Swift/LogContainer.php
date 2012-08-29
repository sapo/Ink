<?php

/**
 * A registry for the logger object.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Log
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";
Swift_ClassLoader::load("Swift_Log_DefaultLog");

$GLOBALS["_SWIFT_LOG"] = null;

/**
 * A registry holding the current instance of the log.
 * @package Swift_Log
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_LogContainer
{ 
  /**
   * Registers the logger.
   * @param Swift_Log The log
   */
  function setLog(&$log)
  {
    if (!is_a($log, "Swift_Log") && !is_a($log, "SimpleMock")) //Grrr???
    {
      trigger_error("Swift_LogContainer::setLog() expects parameter 1 to be of type Swift_Log.");
      return;
    }
    $GLOBALS["_SWIFT_LOG"] =& $log;
  }
  /**
   * Returns the current instance of the log, or lazy-loads the default one.
   * @return Swift_Log
   */
  function &getLog()
  {
    if ($GLOBALS["_SWIFT_LOG"] === null)
    {
      Swift_LogContainer::setLog(new Swift_Log_DefaultLog());
    }
    return $GLOBALS["_SWIFT_LOG"];
  }
}
