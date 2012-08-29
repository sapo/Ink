<?php

/**
 * Swift Mailer SMTP Connection component.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Connection
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_ConnectionBase");

if (!defined("SWIFT_SMTP_ENC_TLS")) define("SWIFT_SMTP_ENC_TLS", 2);
if (!defined("SWIFT_SMTP_ENC_SSL")) define("SWIFT_SMTP_ENC_SSL", 4);
if (!defined("SWIFT_SMTP_ENC_OFF")) define("SWIFT_SMTP_ENC_OFF", 8);
if (!defined("SWIFT_SMTP_PORT_DEFAULT")) define("SWIFT_SMTP_PORT_DEFAULT", 25);
if (!defined("SWIFT_SMTP_PORT_SECURE")) define("SWIFT_SMTP_PORT_SECURE", 465);
if (!defined("SWIFT_SMTP_AUTO_DETECT")) define("SWIFT_SMTP_AUTO_DETECT", -2);

/**
 * Swift SMTP Connection
 * @package Swift_Connection
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Connection_SMTP extends Swift_ConnectionBase
{
  /**
   * Constant for TLS connections
   */
  var $ENC_TLS = SWIFT_SMTP_ENC_TLS;
  /**
   * Constant for SSL connections
   */
  var $ENC_SSL = SWIFT_SMTP_ENC_SSL;
  /**
   * Constant for unencrypted connections
   */
  var $ENC_OFF = SWIFT_SMTP_ENC_OFF;
  /**
   * Constant for the default SMTP port
   */
  var $PORT_DEFAULT = SWIFT_SMTP_PORT_DEFAULT;
  /**
   * Constant for the default secure SMTP port
   */
  var $PORT_SECURE = SWIFT_SMTP_PORT_SECURE;
  /**
   * Constant for auto-detection of paramters
   */
  var $AUTO_DETECT = SWIFT_SMTP_AUTO_DETECT;
  /**
   * A connection handle
   * @var resource
   */
  var $handle = null;
  /**
   * The remote port number
   * @var int
   */
  var $port = null;
  /**
   * Encryption type to use
   * @var int
   */
  var $encryption = null;
  /**
   * A connection timeout
   * @var int
   */
  var $timeout = 15;
  /**
   * A username to authenticate with
   * @var string
   */
  var $username = false;
  /**
   * A password to authenticate with
   * @var string
   */
  var $password = false;
  /**
   * Loaded authentication mechanisms
   * @var array
   */
  var $authenticators = array();
  /**
   * Fsockopen() error codes.
   * @var int
   */
  var $errno;
  /**
   * Fsockopen() error codes.
   * @var string
   */
  var $errstr;
  
  /**
   * Constructor
   * @param string The remote server to connect to
   * @param int The remote port to connect to
   * @param int The encryption level to use
   */
  function Swift_Connection_SMTP($server="localhost", $port=null, $encryption=null)
  {
    if(PHP_VERSION < 5) register_shutdown_function(array(&$this, "__destruct"));
    $this->setServer($server);
    $this->setEncryption($encryption);
    $this->setPort($port);
  }
  /**
   * Set the timeout to connect in seconds
   * @param int Timeout to use
   */
  function setTimeout($time)
  {
    $this->timeout = (int) $time;
  }
  /**
   * Get the timeout currently set for connecting
   * @return int
   */
  function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * Set the remote server to connect to as a FQDN
   * @param string Server name
   */
  function setServer($server)
  {
    if ($server == $this->AUTO_DETECT)
    {
      $server = @ini_get("SMTP");
      if (!$server) $server = "localhost";
    }
    $this->server = (string) $server;
  }
  /**
   * Get the remote server name
   * @return string
   */
  function getServer()
  {
    return $this->server;
  }
  /**
   * Set the remote port number to connect to
   * @param int Port number
   */
  function setPort($port)
  {
    if ($port == $this->AUTO_DETECT)
    {
      $port = @ini_get("SMTP_PORT");
    }
    if (!$port) $port = ($this->getEncryption() == $this->ENC_OFF) ? $this->PORT_DEFAULT : $this->PORT_SECURE;
    $this->port = (int) $port;
  }
  /**
   * Get the remote port number currently used to connect
   * @return int
   */
  function getPort()
  {
    return $this->port;
  }
  /**
   * Provide a username for authentication
   * @param string The username
   */
  function setUsername($user)
  {
    $this->username = $user;
  }
  /**
   * Get the username for authentication
   * @return string
   */
  function getUsername()
  {
    return $this->username;
  }
  /**
   * Set the password for SMTP authentication
   * @param string Password to use
   */
  function setPassword($pass)
  {
    $this->password = $pass;
  }
  /**
   * Get the password for authentication
   * @return string
   */
  function getPassword()
  {
    return $this->password;
  }
  /**
   * Add an authentication mechanism to authenticate with
   * @param Swift_Authenticator
   */
  function attachAuthenticator(&$auth)
  {
    if (!is_a($auth, "Swift_Authenticator") && !is_a($auth, "SimpleMock"))
    {
      trigger_error("Swift_Connection_SMTP::attachAuthenticator expects parameter 1 to be instance of Swift_Authenticator.");
      return;
    }
    $this->authenticators[$auth->getAuthExtensionName()] =& $auth;
    $log =& Swift_LogContainer::getLog();
    if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
    {
      $log->add("Authentication mechanism '" . $auth->getAuthExtensionName() . "' attached.");
    }
  }
  /**
   * Set the encryption level to use on the connection
   * See the constants ENC_TLS, ENC_SSL and ENC_OFF
   * NOTE: PHP needs to have been compiled with OpenSSL for SSL and TLS to work
   * NOTE: Some PHP installations will not have the TLS stream wrapper
   * @param int Level of encryption
   */
  function setEncryption($enc)
  {
    if (!$enc) $enc = $this->ENC_OFF;
    $this->encryption = (int) $enc;
  }
  /**
   * Get the current encryption level used
   * This method returns an integer corresponding to one of the constants ENC_TLS, ENC_SSL or ENC_OFF
   * @return int
   */
  function getEncryption()
  {
    return $this->encryption;
  }
  /**
   * Read a full response from the buffer
   * inner !feof() patch provided by Christian Rodriguez:
   * <a href="http://www.flyspray.org/">www.flyspray.org</a>
   * @return string
   * @throws Swift_ConnectionException Upon failure to read
   */
  function read()
  {
    if (!$this->handle)
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "The SMTP connection is not alive and cannot be read from." . $this->smtpErrors()));
      return;
    }
    $ret = "";
    $line = 0;
    while (!feof($this->handle))
    {
      $line++;
      stream_set_timeout($this->handle, $this->timeout);
      $tmp = @fgets($this->handle);
      if ($tmp === false && !feof($this->handle))
      {
        Swift_Errors::trigger(new Swift_ConnectionException(
          "There was a problem reading line " . $line . " of an SMTP response. The response so far was:<br />[" . $ret .
          "].  It appears the connection has died without saying goodbye to us! Too many emails in one go perhaps?" .
          $this->smtpErrors()));
        return;
      }
      $ret .= trim($tmp) . "\r\n";
      if ($tmp{3} == " ") break;
    }
    return $ret = substr($ret, 0, -2);
  }
  /**
   * Write a command to the server (leave off trailing CRLF)
   * @param string The command to send
   * @throws Swift_ConnectionException Upon failure to write
   */
  function write($command, $end="\r\n")
  {
    if (!$this->handle)
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "The SMTP connection is not alive and cannot be written to." . $this->smtpErrors()));
      return;
    }
    if (!@fwrite($this->handle, $command . $end)  && !empty($command))
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "The SMTP connection did not allow the command '" . $command . "' to be sent." . $this->smtpErrors()));
      return;
    }
  }
  /**
   * Try to start the connection
   * @throws Swift_ConnectionException Upon failure to start
   */
  function start()
  {
    if ($this->port === null)
    {
      switch ($this->encryption)
      {
        case $this->ENC_TLS: case $this->ENC_SSL:
          $this->port = 465;
        break;
        case null: default:
          $this->port = 25;
        break;
      }
    }
    
    $server = $this->server;
    if ($this->encryption == $this->ENC_TLS) $server = "tls://" . $server;
    elseif ($this->encryption == $this->ENC_SSL) $server = "ssl://" . $server;
    
    $log =& Swift_LogContainer::getLog();
    if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
    {
      $log->add("Trying to connect to SMTP server at '" . $server . ":" . $this->port);
    }
    
    if (!$this->handle = @fsockopen($server, $this->port, $errno, $errstr, $this->timeout))
    {
      $this->handle = null;
      $error_msg = "The SMTP connection failed to start [" . $server . ":" . $this->port .
      "]: fsockopen returned Error Number " . $errno . " and Error String '" . $errstr . "'";
      if ($log->isEnabled())
      {
        $log->add($error_msg, SWIFT_LOG_ERROR);
      }
      Swift_Errors::trigger(new Swift_ConnectionException($error_msg));
      return;
    }
    $this->errno =& $errno;
    $this->errstr =& $errstr;
  }
  /**
   * Get the smtp error string as recorded by fsockopen()
   * @return string
   */
  function smtpErrors()
  {
    return " (fsockopen: " . $this->errstr . "#" . $this->errno . ") ";
  }
  /**
   * Authenticate if required to do so
   * @param Swift An instance of Swift
   * @throws Swift_ConnectionException If authentication fails
   */
  function postConnect(&$instance)
  {
    if ($this->getUsername() && $this->getPassword())
    {
      $this->runAuthenticators($this->getUsername(), $this->getPassword(), $instance);
    }
  }
  /**
   * Run each authenticator in turn an try for a successful login
   * If none works, throw an exception
   * @param string Username
   * @param string Password
   * @param Swift An instance of swift
   * @throws Swift_ConnectionException Upon failure to authenticate
   */
  function runAuthenticators($user, $pass, &$swift)
  {
    $log =& Swift_LogContainer::getLog();
    if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
    {
      $log->add("Trying to authenticate with username '" . $user . "'.");
    }
    //Load in defaults
    if (empty($this->authenticators))
    {
      if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
      {
        $log->add("No authenticators loaded; looking for defaults.");
      }
      $dir = dirname(__FILE__) . "/../Authenticator";
      $handle = opendir($dir);
      while (false !== $file = readdir($handle))
      {
        if (preg_match("/^[A-Za-z0-9-]+\\.php\$/", $file))
        {
          $name = preg_replace('/[^a-zA-Z0-9]+/', '', substr($file, 0, -4));
          require_once $dir . "/" . $file;
          $class = "Swift_Authenticator_" . $name;
          $this->attachAuthenticator(new $class());
        }
      }
      closedir($handle);
    }
    
    $tried = 0;
    $looks_supported = true;
    
    //Allow everything we have if the server has the audacity not to help us out.
    if (!$this->hasExtension("AUTH"))
    {
      if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
      {
        $log->add("Server (perhaps wrongly) is not advertising AUTH... manually overriding.");
      }
      $looks_supported = false;
      $this->setExtension("AUTH", array_keys($this->authenticators));
    }
    
    foreach ($this->authenticators as $name => $obj)
    {
      //Server supports this authentication mechanism
      if (in_array($name, $this->getAttributes("AUTH")) || $name{0} == "*")
      {
        $tried++;
        if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
        {
          $log->add("Trying '" . $name . "' authentication...");
        }
        if ($this->authenticators[$name]->isAuthenticated($user, $pass, $swift))
        {
          if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
          {
            $log->add("Success! Authentication accepted.");
          }
          return true;
        }
      }
    }
    
    //Server doesn't support authentication
    if (!$looks_supported && $tried == 0)
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "Authentication is not supported by the server but a username and password was given."));
      return;
    }
    
    if ($tried == 0)
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "No authentication mechanisms were tried since the server did not support any of the ones loaded. " .
        "Loaded authenticators: [" . implode(", ", array_keys($this->authenticators)) . "]"));
    }
    else
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "Authentication failed using username '" . $user . "' and password '". str_repeat("*", strlen($pass)) . "'"));
    }
  }
  /**
   * Try to close the connection
   * @throws Swift_ConnectionException Upon failure to close
   */
  function stop()
  {
    $log =& Swift_LogContainer::getLog();
    if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
    {
      $log->add("Closing down SMTP connection.");
    }
    if ($this->handle)
    {
      if (!fclose($this->handle))
      {
        Swift_Errors::trigger(new Swift_ConnectionException(
          "The SMTP connection could not be closed for an unknown reason." . $this->smtpErrors()));
        return;
      }
      $this->handle = null;
    }
  }
  /**
   * Check if the SMTP connection is alive
   * @return boolean
   */
  function isAlive()
  {
    return ($this->handle !== null);
  }
  /**
   * Destructor.
   * Cleans up any open connections.
   */
  function __destruct()
  {
    $this->stop();
  }
}
