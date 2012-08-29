<?php

/**
 * Swift Mailer Multiple Redundant Cycling Connection component.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Connection
 * @license GNU Lesser General Public License
 */
 
require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_ConnectionBase");

/**
 * Swift Rotator Connection
 * Switches through each connection in turn after sending each message
 * @package Swift_Connection
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Connection_Rotator extends Swift_ConnectionBase
{
  /**
   * The list of available connections
   * @var array
   */
  var $connections = array();
  /**
   * The id of the active connection
   * @var int
   */
  var $active = null;
  /**
   * Contains a list of any connections which were tried but found to be dead
   * @var array
   */
  var $dead = array();
  
  /**
   * Constructor
   */
  function Swift_Connection_Rotator($connections=array())
  {
    foreach ($connections as $id => $conn)
    {
      $this->addConnection($connections[$id], $id);
    }
  }
  /**
   * Add a connection to the list of options
   * @param Swift_Connection An instance of the connection
   */
  function addConnection(&$connection)
  {
    if (!is_a($connection, "Swift_Connection") && !is_a($connection, "SimpleMock"))
    {
      trigger_error("Swift_Connection_Rotator::addConnection expects parameter 1 to be instance of Swift_Connection.");
      return;
    }
    $log =& Swift_LogContainer::getLog();
    if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
    {
      $log->add("Adding new connection of type '" . get_class($connection) . "' to rotator.");
    }
    $this->connections[] =& $connection;
  }
  /**
   * Rotate to the next working connection
   * @throws Swift_ConnectionException If no connections are available
   */
  function nextConnection()
  {
    $log =& Swift_LogContainer::getLog();
    if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
    {
      $log->add(" <==> Rotating connection.");
    }
    
    $total = count($this->connections);
    $start = $this->active === null ? 0 : ($this->active + 1);
    if ($start >= $total) $start = 0;
    
    $fail_messages = array();
    for ($id = $start; $id < $total; $id++)
    {
      if (in_array($id, $this->dead)) continue; //The connection was previously found to be useless
      
      Swift_Errors::expect($e, "Swift_ConnectionException");
        if (!$this->connections[$id]->isAlive()) $this->connections[$id]->start();
      if (!$e) {
        if ($this->connections[$id]->isAlive())
        {
          Swift_Errors::clear("Swift_ConnectionException");
          $this->active = $id;
          return true;
        }
        $this->dead[] = $id;
        $this->connections[$id]->stop();
        Swift_Errors::trigger(new Swift_ConnectionException(
          "The connection started but reported that it was not active"));
      }
      $this->dead[] = $id;
      $this->connections[$id]->stop();
      $fail_messages[] = $id . ": " . $e->getMessage();
      $e = null;
    }
    $failure = implode("<br />", $fail_messages);
    Swift_Errors::trigger(new Swift_ConnectionException(
      "No connections were started.<br />" . $failure));
  }
  /**
   * Read a full response from the buffer
   * @return string
   * @throws Swift_ConnectionException Upon failure to read
   */
  function read()
  {
    if ($this->active === null)
    {
      Swift_Errors::trigger(new Swift_ConnectionException("None of the connections set have been started"));
      return;
    }
    return $this->connections[$this->active]->read();
  }
  /**
   * Write a command to the server (leave off trailing CRLF)
   * @param string The command to send
   * @throws Swift_ConnectionException Upon failure to write
   */
  function write($command, $end="\r\n")
  {
    if ($this->active === null)
    {
      Swift_Errors::trigger(new Swift_ConnectionException("None of the connections set have been started"));
      return;
    }
    return $this->connections[$this->active]->write($command, $end);
  }
  /**
   * Try to start the connection
   * @throws Swift_ConnectionException Upon failure to start
   */
  function start()
  {
    if ($this->active === null) return $this->nextConnection();
  }
  /**
   * Try to close the connection
   * @throws Swift_ConnectionException Upon failure to close
   */
  function stop()
  {
    foreach ($this->connections as $id => $conn)
    {
      if ($this->connections[$id]->isAlive()) $this->connections[$id]->stop();
    }
    $this->active = null;
  }
  /**
   * Check if the current connection is alive
   * @return boolean
   */
  function isAlive()
  {
    return (($this->active !== null) && $this->connections[$this->active]->isAlive());
  }
  /**
   * Get the ID of the active connection
   * @return int
   */
  function getActive()
  {
    return $this->active;
  }
  /**
   * Call the current connection's postConnect() method
   */
  function postConnect(&$instance)
  {
    Swift_ClassLoader::load("Swift_Plugin_ConnectionRotator");
    if (!$instance->getPlugin("_ROTATOR")) $instance->attachPlugin(new Swift_Plugin_ConnectionRotator(), "_ROTATOR");
    $this->connections[$this->active]->postConnect($instance);
  }
  /**
   * Call the current connection's setExtension() method
   */
  function setExtension($extension, $attributes=array())
  {
    $this->connections[$this->active]->setExtension($extension, $attributes);
  }
  /**
   * Call the current connection's hasExtension() method
   */
  function hasExtension($name)
  {
    return $this->connections[$this->active]->hasExtension($name);
  }
  /**
   * Call the current connection's getAttributes() method
   */
  function getAttributes($name)
  {
    return $this->connections[$this->active]->getAttributes($name);
  }
}
