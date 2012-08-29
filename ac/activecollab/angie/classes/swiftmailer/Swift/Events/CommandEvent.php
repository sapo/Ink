<?php

/**
 * Swift Mailer Command Event
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Events");

/**
 * Generated when Swift is sending a command
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Events_CommandEvent extends Swift_Events
{
  /**
   * Contains the command being sent
   * @var string
   */
  var $string = null;
  /**
   * Contains the expected response code (or null)
   * @var int
   */
  var $code = null;
  
  /**
   * Constructor
   * @param string The command being sent
   * @param int The expected code
   */
  function Swift_Events_CommandEvent($string, $code=null)
  {
    $this->setString($string);
    $this->setCode($code);
  }
  /**
   * Set the command being sent (without CRLF)
   * @param string The command being sent
   */
  function setString($string)
  {
    $this->string = (string) $string;
  }
  /**
   * Get the command being sent
   * @return string
   */
  function getString()
  {
    return $this->string;
  }
  /**
   * Set response code which is expected
   * @param int The response code
   */
  function setCode($code)
  {
    if ($code === null) $this->code = null;
    else $this->code = (int) $code;
  }
  /**
   * Get the expected response code
   * @return int
   */
  function getCode()
  {
    return $this->code;
  }
}
