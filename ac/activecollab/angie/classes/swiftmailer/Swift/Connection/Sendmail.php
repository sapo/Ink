<?php

/**
 * Swift Mailer Sendmail Connection component.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Connection
 * @license GNU Lesser General Public License
 */
 
require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_ConnectionBase");

if (!defined("SWIFT_SENDMAIL_AUTO_DETECT")) define("SWIFT_SENDMAIL_AUTO_DETECT", -2);

//Sorry guys, it has to be done and it's not my lazy coding, blame PHP4/proc_open()
$GLOBALS["_SWIFT_PROC"] = array();

/**
 * Swift Sendmail Connection
 * @package Swift_Connection
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Connection_Sendmail extends Swift_ConnectionBase
{
  /**
   * Constant for auto-detection of paths
   */
  var $AUTO_DETECT = SWIFT_SENDMAIL_AUTO_DETECT;
  /**
   * Flags for the MTA (options such as bs or t)
   * @var string
   */
  var $flags = null;
  /**
   * The full path to the MTA
   * @var string
   */
  var $path = null;
  /**
   * The type of last request sent
   * For example MAIL, RCPT, DATA
   * @var string
   */
  var $request = null;
  /**
   * The process handle
   * @var resource
   */
  var $proc;
  /**
   * I/O pipes for the process
   * @var array
   */
  var $pipes;
  /**
   * Switches to true for just one command when DATA has been issued
   * @var boolean
   */
  var $send = false;
  /**
   * The timeout in seconds before giving up
   * @var int Seconds
   */
  var $timeout = 10;
  
  /**
   * Constructor
   * @param string The command to execute
   * @param int The timeout in seconds before giving up
   */
  function Swift_Connection_Sendmail($command="/usr/sbin/sendmail -bs", $timeout=10)
  {
    if(PHP_VERSION < 5) register_shutdown_function(array(&$this, "__destruct"));
    $this->setCommand($command);
    $this->setTimeout($timeout);
  }
  /**
   * Set the timeout on the process
   * @param int The number of seconds
   */
  function setTimeout($secs)
  {
    $this->timeout = (int)$secs;
  }
  /**
   * Get the timeout on the process
   * @return int
   */
  function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * Set the operating flags for the MTA
   * @param string
   */
  function setFlags($flags)
  {
    $this->flags = $flags;
  }
  /**
   * Get the operating flags for the MTA
   * @return string
   */
  function getFlags()
  {
    return $this->flags;
  }
  /**
   * Set the path to the binary
   * @param string The path (must be absolute!)
   */
  function setPath($path)
  {
    if ($path == $this->AUTO_DETECT) $path = $this->findSendmail();
    $this->path = $path;
  }
  /**
   * Get the path to the binary
   * @return string
   */
  function getPath()
  {
    return $this->path;
  }
  /**
   * For auto-detection of sendmail path
   * Thanks to "Joe Cotroneo" for providing the enhancement
   * @return string
   */
  function findSendmail()
  {
    $log =& Swift_LogContainer::getLog();
    if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
    {
      $log->add("Sendmail path auto-detection in progress.  Trying `which sendmail`");
    }
    $path = @trim(shell_exec('which sendmail'));
    if (!is_executable($path))
    {
      if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
      {
        $log->add("No luck so far, trying some common paths...");
      }
      $common_locations = array(
        '/usr/bin/sendmail',
        '/usr/lib/sendmail',
        '/var/qmail/bin/sendmail',
        '/bin/sendmail',
        '/usr/sbin/sendmail',
        '/sbin/sendmail'
      );
      foreach ($common_locations as $path)
      {
        if (is_executable($path)) return $path;
      }
      if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
      {
        $log->add("Falling back to /usr/sbin/sendmail (but it doesn't look good)!");
      }
      //Fallback (swift will still throw an error)
      return "/usr/sbin/sendmail";
    }
    else return $path;
  }
  /**
   * Set the sendmail command (path + flags)
   * @param string Command
   * @throws Swift_ConnectionException If the command is not correctly structured
   */
  function setCommand($command)
  {
    if ($command == $this->AUTO_DETECT) $command = $this->findSendmail() . " -bs";
    
    if (!strrpos($command, " -"))
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "Cannot set sendmail command with no command line flags. e.g. /usr/sbin/sendmail -t"));
      return;
    }
    $path = substr($command, 0, strrpos($command, " -"));
    $flags = substr($command, strrpos($command, " -")+2);
    $this->setPath($path);
    $this->setFlags($flags);
  }
  /**
   * Get the sendmail command (path + flags)
   * @return string
   */
  function getCommand()
  {
    return $this->getPath() . " -" . $this->getFlags();
  }
  /**
   * Write a command to the open pipe
   * @param string The command to write
   * @throws Swift_ConnectionException If the pipe cannot be written to
   */
  function pipeIn($command, $end="\r\n")
  {
    if (!$this->isAlive())
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "The sendmail process is not alive and cannot be written to."));
      return;
    }
    if (!@fwrite($this->pipes[0], $command . $end)  && !empty($command))
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "The sendmail process did not allow the command '" . $command . "' to be sent."));
    }
    fflush($this->pipes[0]);
  }
  /**
   * Read data from the open pipe
   * @return string
   * @throws Swift_ConnectionException If the pipe is not operating as expected
   */
  function pipeOut()
  {
    if (strpos($this->getFlags(), "t") !== false) return;
    if (!$this->isAlive())
    {
      Swift_Errors::trigger(new Swift_ConnectionException("The sendmail process is not alive and cannot be read from."));
      return;
    }
    $ret = "";
    $line = 0;
    while (true)
    {
      $line++;
      stream_set_timeout($this->pipes[1], $this->timeout);
      $tmp = @fgets($this->pipes[1]);
      if ($tmp === false)
      {
        Swift_Errors::trigger(new Swift_ConnectionException(
          "There was a problem reading line " . $line . " of a sendmail SMTP response. The response so far was:<br />[" . $ret . "].  It appears the process has died."));
        return;
      }
      $ret .= trim($tmp) . "\r\n";
      if ($tmp{3} == " ") break;
    }
    fflush($this->pipes[1]);
    return $ret = substr($ret, 0, -2);
  }
  /**
   * Read a full response from the buffer (this is spoofed if running in -t mode)
   * @return string
   * @throws Swift_ConnectionException Upon failure to read
   */
  function read()
  {
    if ($this->getFlags() == "t")
    {
      switch (strtolower($this->request))
      {
        case null:
          return "220 Greetings";
        case "helo": case "ehlo":
          return "250 hello";
        case "mail": case "rcpt": case "rset":
          return "250 ok";
        case "quit":
          return "221 bye";
        case "data":
          $this->send = true;
          return "354 go ahead";
        default:
          return "250 ok";
      }
    }
    else return $this->pipeOut();
  }
  /**
   * Write a command to the process (leave off trailing CRLF)
   * @param string The command to send
   * @throws Swift_ConnectionException Upon failure to write
   */
  function write($command, $end="\r\n")
  {
    if (strpos($this->getFlags(), "t") !== false)
    {
      if (!$this->send && strpos($command, " ")) $command = substr($command, strpos($command, " ")+1);
      elseif ($this->send)
      {
        $this->pipeIn($command, $end);
      }
      $this->request = $command;
      $this->send = (strtolower($command) == "data");
    }
    else $this->pipeIn($command);
  }
  /**
   * Try to start the connection
   * @throws Swift_ConnectionException Upon failure to start
   */
  function start()
  {
    $log =& Swift_LogContainer::getLog();
    if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
    {
      $log->add("Trying to start a sendmail process.");
    }
    if (!$this->getPath() || !$this->getFlags())
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "Sendmail cannot be started without a path to the binary including flags."));
      return;
    }
    if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
    {
      $log->add("Trying to stat the executable '" . $this->getPath() . "'.");
    }
    if (!@lstat($this->getPath()))
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "Sendmail cannot be seen with lstat().  The command given [" . $this->getCommand() . "] does not appear to be valid."));
      return;
    }
      
    $pipes_spec = array(
      array("pipe", "r"),
      array("pipe", "w"),
      array("pipe", "w")
    );
    
    $i = count($GLOBALS["_SWIFT_PROC"]);
    $GLOBALS["_SWIFT_PROC"][$i] = proc_open($this->getCommand(), $pipes_spec, $this->pipes);
    $this->proc =& $GLOBALS["_SWIFT_PROC"][$i];
    
    if (!$this->isAlive())
    {
      Swift_Errors::trigger(new Swift_ConnectionException(
        "The sendmail process failed to start.  Please verify that the path exists and PHP has permission to execute it."));
      return;
    }
  }
  /**
   * Try to close the connection
   */
  function stop()
  {
    $log =& Swift_LogContainer::getLog();
    if ($log->hasLevel(SWIFT_LOG_EVERYTHING))
    {
      $log->add("Terminating sendmail process.");
    }
    foreach ((array)$this->pipes as $pipe)
    {
      @fclose($pipe);
    }
    
    if ($this->proc)
    {
      proc_close($this->proc);
      $this->pipes = null;
      $this->proc = null;
    }
  }
  /**
   * Check if the process is still alive
   * @return boolean
   */
  function isAlive()
  {
    return (is_resource($this->proc)
      && is_resource($this->pipes[0])
      && is_resource($this->pipes[1])
      && $this->proc !== null);
  }
  /**
   * Destructor.
   * Cleans up by stopping any running processes.
   */
  function __destruct()
  {
    $this->stop();
  }
}
