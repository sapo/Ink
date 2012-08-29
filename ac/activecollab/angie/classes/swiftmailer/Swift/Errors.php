<?php

/**
 * Swift Mailer PHP4 Exception hackaround.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */


/**
 * Swift Exception handling object for PHP4
 * Triggers and/or catches errors
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Errors
{
  /**
   * Caught errors
   * @var array,Swift_Error
   */
  var $errors = array();
  /**
   * If an error has been thrown previously and not caught (hack)
   * @var boolean
   */
  var $halt = false;
  /**
   * Errors we're expecting, so don't trigger them
   * @var array
   */
  var $try = array();
  
  /**
   * Get an instance of this class as a singleton - needed internally
   * @return Swift_Errors
   */
  function &getInstance()
  {
    static $instance = null;
    if (!$instance) $instance = array(new Swift_Errors());
    return $instance[0];
  }
  /**
   * Check if things are supposed to have stopped processing because of an
   * uncaught excpetion
   * @return boolean
   */
  function halted()
  {
    $me =& Swift_Errors::getInstance();
    return $me->halt;
  }
  /**
   * Reset everything logged so far
   */
  function reset()
  {
    $me =& Swift_Errors::getInstance();
    $me->errors = array();
    $me->halt = false;
    $me->try = array();
  }
  /**
   * Throw a new exception - it will either be caught or triggered
   * @param Swift_Exception
   */
  function trigger(&$e)
  {
    $me =& Swift_Errors::getInstance();
    $me->errors[] =& $e;
    $me->halt = true;
    foreach (array_reverse(array_keys($me->try)) as $type)
    {
      if (is_a($e, $type))
      {
        foreach (array_reverse(array_keys($me->try[$type])) as $i)
        {
          $me->try[$type][$i] = $e;
          unset($me->try[$type][$i]);
          $me->halt = false;
          return;
        }
      }
    }
    //If here, then it wasn't caught
    $me->dumpError($e);
  }
  /**
   * Dump the error if it was not caught
   * @param Swift_Exception
   */
  function dumpError(&$e)
  {
    $output = "<br /><strong>Uncaught Error</strong> of type [" . get_class($e) . "] with message [" . $e->getMessage() . "]";
    $output .= "<br />" . $e->getBacktraceDump() . "<br />";
    trigger_error($output, E_USER_ERROR);
  }
  /**
   * Tell the error handler we're expecting an error of type $type and assign it to $e
   * @param &$e
   * @param string The type of expection - optional
   */
  function expect(&$e, $type="Swift_Exception")
  {
    $me =& Swift_Errors::getInstance();
    $e = null;
    $me->try[$type][] =& $e;
  }
  /**
   * Clear anything that may have been expected matching $type
   * @param string The type
   */
  function clear($type)
  {
    $me =& Swift_Errors::getInstance();
    if (isset($me->try[$type]))
    {
      foreach (array_reverse(array_keys($me->try[$type])) as $i)
      {
        unset($me->try[$type][$i]);
        break;
      }
    }
  }
  /**
   * The last error message as a string
   * @return string
   */
  function getLast()
  {
    $me =& Swift_Errors::getInstance();
    if (count($me->errors))
    {
      $last =& $me->errors[(count($me->errors)-1)];
      return $last->getMessage();
    }
  }
  /**
   * Get all logged errors as an array
   * @return array,Swift_Exception
   */
  function &getAll()
  {
    return $this->errors;
  }
}
