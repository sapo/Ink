<?php

/**
 * Swift Mailer Bandwidth Monitoring Plugin
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Plugin
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";

/**
 * Swift Bandwidth Monitor.
 * Tracks bytes in and out of the connection.
 * @package Swift_Plugin
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Plugin_BandwidthMonitor extends Swift_Events_Listener
{
  /**
   * The number of bytes received
   * @var int
   */
  var $in = 0;
  /**
   * The number of bytes sent
   * @var int
   */
  var $out = 0;
  
  /**
   * Part of the interface which is notified after a command is sent.
   * @param Swift_Events_CommandEvent
   */
  function commandSent(&$e)
  {
    $code = $e->getCode();
    $add = 0;
    if ($code != -1) $add = 2;
    $bytes = strlen($e->getString()) + $add;
    $this->addBytesOut($bytes);
  }
  /**
   * Part of the interface which is notified when a response is received
   * @param Swift_Events_ResponseEvent
   */
  function responseReceived(&$e)
  {
    $bytes = strlen($e->getString()) + 2;
    $this->addBytesIn($bytes);
  }
  /**
   * Add some bytes to the running totals for incoming bandwidth
   * @param int Bytes in
   */
  function addBytesIn($num)
  {
    $num = abs((int)$num);
    $this->setBytesIn($this->getBytesIn() + $num);
  }
  /**
   * Add some bytes to the running totals for outgoing bandwidth
   * @param int Bytes out
   */
  function addBytesOut($num)
  {
    $num = abs((int)$num);
    $this->setBytesOut($this->getBytesOut() + $num);
  }
  /**
   * Get the total number of bytes received
   * @return int
   */
  function getBytesIn()
  {
    return $this->in;
  }
  /**
   * Get the total number of bytes sent
   * @return int
   */
  function getBytesOut()
  {
    return $this->out;
  }
  /**
   * Set the total number of bytes received.
   * Can be used to reset the counters at runtime.
   * @param int The bytes in
   */
  function setBytesIn($num)
  {
    $this->in = abs((int)$num);
  }
  /**
   * Set the total number of bytes sent.
   * Can be used to reset the counters at runtime.
   * @param int The bytes out
   */
  function setBytesOut($num)
  {
    $this->out = abs((int)$num);
  }
}
