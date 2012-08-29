<?php

/**
 * Swift Mailer Events Layer
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */

/**
 * Provides core functionality for Swift generated events for plugins
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Events /*abstract*/
{
  /**
   * An instance of Swift
   * @var Swift
   */
  var $swift = null;
  
  /**
   * Provide a reference to te currently running Swift this event was generated from
   * @param Swift
   */
  function setSwift(&$swift)
  {
    $this->swift =& $swift;
  }
  /**
   * Get the current instance of swift
   * @return Swift
   */
  function &getSwift()
  {
    return $this->swift;
  }
}
