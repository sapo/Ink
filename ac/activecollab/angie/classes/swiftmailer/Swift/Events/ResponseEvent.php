<?php

/**
 * Swift Mailer Response Event
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Events");

/**
 * Generated when Swift receives a server response
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Events_ResponseEvent extends Swift_Events
{
  /**
   * Contains the response received
   * @var string
   */
  var $string = null;
  /**
   * Contains the response code
   * @var int
   */
  var $code = null;
  
  /**
   * Constructor
   * @param string The received response
   */
  function Swift_Events_ResponseEvent($string)
  {
    $this->setString($string);
    $this->setCode(substr($string, 0, 3));
  }
  /**
   * Set the response received
   * @param string The response
   */
  function setString($string)
  {
    $this->string = (string) $string;
  }
  /**
   * Get the received response
   * @return string
   */
  function getString()
  {
    return $this->string;
  }
  /**
   * Set response code
   * @param int The response code
   */
  function setCode($code)
  {
    $this->code = (int) $code;
  }
  /**
   * Get the response code
   * @return int
   */
  function getCode()
  {
    return $this->code;
  }
}
