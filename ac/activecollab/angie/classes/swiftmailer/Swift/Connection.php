<?php

/**
 * Swift Mailer Connection Interface
 * All connection handlers extend this abstract class
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Connection
 * @license GNU Lesser General Public License
 */

/**
 * Swift Connection Interface
 * Lists methods which are required by any connections
 * @package Swift_Connection
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Connection /*interface*/
{
  /**
   * Try to start the connection
   * @throws Swift_ConnectionException If the connection cannot be started
   */
  function start() {}
  /**
   * Return the contents of the buffer
   * @return string
   * @throws Swift_ConnectionException If the buffer cannot be read
   */
  function read() {}
  /**
   * Write a command to the buffer
   * @param string The command to send
   * @throws Swift_ConnectionException If the write fails
   */
  function write($command) {}
  /**
   * Try to stop the connection
   * @throws Swift_ConnectionException If the connection cannot be closed/stopped
   */
  function stop() {}
  /**
   * Check if the connection is up or not
   * @return boolean
   */
  function isAlive() {}
  /**
   * Add an extension which is available on this connection
   * @param string The name of the extension
   * @param array The list of attributes for the extension
   */
  function setExtension($name, $list=array()) {}
  /**
   * Check if an extension exists by the name $name
   * @param string The name of the extension
   * @return boolean
   */
  function hasExtension($name) {}
  /**
   * Get the list of attributes for the extension $name
   * @param string The name of the extension
   * @return array
   * @throws Swift_ConnectionException If no such extension can be found
   */
  function getAttributes($name) {}
  /**
   * Execute logic needed after SMTP greetings
   * @param Swift An instance of Swift
   */
  function postConnect(/*Swift*/ $instance) {}
  /**
   * Returns TRUE if the connection needs a EHLO greeting.
   * @return boolean
   */
  function getRequiresEHLO() {}
  /**
   * Set if the connection needs a EHLO greeting.
   * @param boolean
   */
  function setRequiresEHLO($set) {}
}
