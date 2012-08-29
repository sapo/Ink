<?php

/**
 * Swift Mailer AntiFlood Plugin
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Plugin
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Events_Listener");

/**
 * Swift AntiFlood controller.
 * Closes a connection and pauses for X seconds after a number of emails have been sent.
 * @package Swift_Plugin
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Plugin_AntiFlood extends Swift_Events_Listener
{
  /**
   * The number of emails to send between connections
   * @var int
   */
  var $threshold = null;
  /**
   * The number of seconds to pause for between connections
   * @var int
   */
  var $waitFor = null;
  /**
   * Number of emails sent so far
   * @var int
   */
  var $count = 0;
  
  /**
   * Constructor
   * @param int Number of emails to send before re-connecting
   * @param int The timeout in seconds between connections
   */
  function Swift_Plugin_AntiFlood($threshold, $wait=0)
  {
    $this->setThreshold($threshold);
    $this->setWait($wait);
  }
  /**
   * Set the number of emails which must be sent for a reconnection to occur
   * @param int Number of emails
   */
  function setThreshold($threshold)
  {
    $this->threshold = (int) $threshold;
  }
  /**
   * Get the number of emails which need to be sent for reconnection to occur
   * @return int
   */
  function getThreshold()
  {
    return $this->threshold;
  }
  /**
   * Set the number of seconds the plugin should wait for before reconnecting
   * @param int Time in seconds
   */
  function setWait($time)
  {
    $this->waitFor = (int) $time;
  }
  /**
   * Get the number of seconds the plugin should wait for before re-connecting
   * @return int
   */
  function getWait()
  {
    return $this->waitFor;
  }
  /**
   * Sleep for a given number of seconds
   * @param int Number of seconds to wait for
   */
  function wait($seconds)
  {
    if ($seconds) sleep($seconds);
  }
  /**
   * Swift's SendEvent listener.
   * Invoked when Swift sends a message
   * @param Swift_Events_SendEvent The event information
   * @throws Swift_ConnectionException If the connection cannot be closed/re-opened
   */
  function sendPerformed(&$e)
  {
    $swift =& $e->getSwift();
    $this->count++;
    if ($this->count >= $this->getThreshold())
    {
      $swift->disconnect();
      $this->wait($this->getWait());
      $swift->connect();
      $this->count = 0;
    }
  }
}
