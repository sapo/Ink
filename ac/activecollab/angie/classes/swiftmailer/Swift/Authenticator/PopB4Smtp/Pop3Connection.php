<?php

/**
 * Swift Mailer PopB4Smtp Pop3 Connection component
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Authenticator
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../../ClassLoader.php";
Swift_ClassLoader::load("Swift_Errors");


if (!defined("SWIFT_AUTH_POP_ENC_SSL")) define("SWIFT_AUTH_POP_ENC_SSL", 1);
if (!defined("SWIFT_AUTH_POP_ENC_OFF")) define("SWIFT_AUTH_POP_ENC_OFF", 0);

/**
 * Swift PopB4Smtp Authenticator Connection Component for the POP3 server
 * Provides a I/O wrapper for the POP3 connection
 * @package Swift_Authenticator
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Authenticator_PopB4Smtp_Pop3Connection
{
  /**
   * Constant for no encyption
   */
  var $ENC_OFF = SWIFT_AUTH_POP_ENC_OFF;
  /**
   * Constant for SSL encryption
   */
  var $ENC_SSL = SWIFT_AUTH_POP_ENC_SSL;
  /**
   * The server to connect to (IP or FQDN)
   * @var string
   */
  var $server = null;
  /**
   * The port to connect to
   * @var int
   */
  var $port = null;
  /**
   * The open connection resource from fsockopen()
   * @var resource
   */
  var $handle = null;
  
  /**
   * Constructor
   * @param string The name of the POP3 server
   * @param int The port for the POP3 service
   * @param int The level of encryption to use
   */
  function Swift_Authenticator_PopB4Smtp_Pop3Connection($server="localhost", $port=110, $encryption=0)
  {
    $this->setServer($server);
    $this->setPort($port);
    $this->setEncryption($encryption);
  }
  /**
   * Set the server name
   * @param string The IP or FQDN of the POP3 server
   */
  function setServer($server)
  {
    $this->server = (string) $server;
  }
  /**
   * Set the port number for the POP3 server
   * @param int
   */
  function setPort($port)
  {
    $this->port = (int) $port;
  }
  /**
   * Get the server name
   * @return string
   */
  function getServer()
  {
    return $this->server;
  }
  /**
   * Get the remote port number
   * @return int
   */
  function getPort()
  {
    return $this->port;
  }
  /**
   * Set the level of enryption to use (see ENC_OFF or ENC_SSL)
   * @param int The constant for the encryption level
   */
  function setEncryption($enc)
  {
    $this->encryption = (int) $enc;
  }
  /**
   * Get the current encryption level set (corresponds to ENC_SSL or ENC_OFF)
   * @return int
   */
  function getEncryption()
  {
    return $this->encryption;
  }
  /**
   * Check if the response is a +OK response
   * @return boolean
   */
  function assertOk($line)
  {
    if (substr($line, 0, 3) != "+OK")
      Swift_Errors::trigger(new Swift_ConnectionException("The POP3 server did not suitably respond with a +OK response. " .
      "[" . $line . "]"));
  }
  /**
   * Try to open the connection
   * @throws Swift_ConnectionException If the connection will not start
   */
  function start()
  {
    $url = $this->getServer();
    if ($this->getEncryption() == $this->ENC_SSL) $url = "ssl://" . $url;
    
    if ((false === $this->handle = fsockopen($url, $this->getPort(), $errno, $errstr, $timeout)))
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "The POP3 connection failed to start.  The error string returned from fsockopen() is [" . $errstr . "] #" . $errno));
      return;
    }
  }
  /**
   * Try to close the connection
   */
  function stop()
  {
    if ($this->handle !== null)
    {
      fclose($this->handle);
    }
    $this->handle = null;
  }
  /**
   * Return the unread buffer contents
   * @return string
   * @throws Swift_ConnectionException If the connection will not allow data to be read
   */
  function read()
  {
    if (false === $response = fgets($this->handle))
    {
      Swift_Errors::trigger(new Swift_ConnectionException("Data could not be read from the POP3 connection."));
      return;
    }
    return trim($response);
  }
  /**
   * Write a command to the remote socket
   * @param string the command to send (without CRLF)
   * @throws Swift_ConnectionException If the command cannot be written
   */
  function write($command)
  {
    if (false !== fwrite($this->handle, $command . "\r\n"))
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "Data could not be written to the POP3 connection."));
      return;
    }
  }
}
