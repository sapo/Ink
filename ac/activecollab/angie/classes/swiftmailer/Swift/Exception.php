<?php

/**
 * Swift Mailer PHP4 Exception.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";
Swift_ClassLoader::load("Swift_LogContainer");

/**
 * Swift Exception for PHP4.
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Exception
{
  /**
   * The error message in this exception
   * @var string
   */
  var $message;
  /**
   * A backtrace to show
   * @var array
   */
  var $trace;
  
  /**
   * Constructor
   * @param string The error message
   */
  function Swift_Exception($message)
  {
    if (($log =& Swift_LogContainer::getLog()) && $log->isEnabled())
    {
      $message .= "<h3>Log Information</h3>";
      $message .= "<pre>" . htmlentities($log->dump(true)) . "</pre>";
    }
    $this->message = $message;
    $this->trace = debug_backtrace();
  }
  /**
   * Get the error message
   * @return string
   */
  function getMessage()
  {
    return $this->message;
  }
  /**
   * Get the backtrace
   * @return array
   */
  function getTrace()
  {
    return $this->trace;
  }
  /**
   * Get a summarised backtrace as a string
   * @return string
   */
  function getBacktraceDump()
  {
    $trace = $this->getTrace();
    $ret = "";
    for ($i = 0; $i < count($trace); $i++)
    {
      $end = array_pop($trace);
      if (!empty($end["class"])) $class = $end["class"] . "::";
      else $class = "";
      
      $file_info = " @$i " . $class . $end["function"] .
      "() in " . $end["file"] . " on line " .
      $end["line"] . "<br />";
      
      $ret .= $file_info;
    }
    return $ret;
  }
}
