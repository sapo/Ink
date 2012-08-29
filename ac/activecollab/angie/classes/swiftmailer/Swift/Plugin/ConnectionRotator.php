<?php

/**
 * Swift Mailer Rotating Connection Controller
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Plugin
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Events_Listener");

/**
 * Swift Rotating Connection Controller
 * Invokes the nextConnection() method of Swift_Connection_Rotator upon sending a given number of messages
 * @package Swift_Plugin
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Plugin_ConnectionRotator extends Swift_Events_Listener
{
  /**
   * The number of emails which must be sent before the connection is rotated
   * @var int Threshold number of emails
   */
  var $threshold = 1;
  /**
   * The total number of emails sent on this connection
   * @var int
   */
  var $count = 0;
  /**
   * The connections we have used thus far
   * @var array
   */
  var $used = array();
  /**
   * Internal check to see if this plugin has yet been invoked
   * @var boolean
   */
  var $called = false;
  
  /**
   * Constructor
   * @param int The number of emails to send before rotating
   */
  function Swift_Plugin_ConnectionRotator($threshold=1)
  {
    $this->setThreshold($threshold);
  }
  /**
   * Set the number of emails to send before a connection rotation is tried
   * @param int Number of emails
   */
  function setThreshold($threshold)
  {
    $this->threshold = (int) $threshold;
  }
  /**
   * Get the number of emails which must be sent before a rotation occurs
   * @return int
   */
  function getThreshold()
  {
    return $this->threshold;
  }
  /**
   * Swift's SendEvent listener.
   * Invoked when Swift sends a message
   * @param Swift_Events_SendEvent The event information
   * @throws Swift_ConnectionException If the connection cannot be rotated
   */
  function sendPerformed(&$e)
  {
    $swift =& $e->getSwift();
    if (!method_exists($swift->connection, "nextConnection"))
    {
      trigger_error("The ConnectionRotator plugin cannot be used with connections other than Swift_Connection_Rotator.");
      return;
    }
    if (!$this->called)
    {
      $this->used[] = $swift->connection->getActive();
    }
    $this->count++;
    if ($this->count >= $this->getThreshold())
    {
      $swift->connection->nextConnection();
      if (!in_array(($id = $swift->connection->getActive()), $this->used))
      {
        $swift->connect();
        $this->used[] = $id;
      }
      $this->count = 0;
    }
    $this->called = true;
  }
  /**
   * Disconnect all the other connections
   * @param Swift_Events_DisconnectEvent The event info
   */
  function disconnectPerformed(&$e)
  {
    $conn =& $e->getConnection();
    $swift =& $e->getSwift();
    $active = $conn->getActive();
    $conn->nextConnection();
    while ($conn->getActive() != $active)
    {
      $swift->command("QUIT", 221);
      $conn->stop();
      $conn->nextConnection();
    }
    $this->used = array();
  }
}
