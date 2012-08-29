<?php

/**
 * Swift Mailer File Stream Wrapper
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";
Swift_ClassLoader::load("Swift_Errors");
Swift_ClassLoader::load("Swift_FileException");

/**
 * Swift File stream abstraction layer
 * Reads bytes from a file
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_File
{
  /**
   * The accessible path to the file
   * @var string
   */
  var $path = null;
  /**
   * The name of the file
   * @var string
   */
  var $name = null;
  /**
   * The resource returned by fopen() against the path
   * @var resource
   */
  var $handle = null;
  /**
   * The status of magic_quotes in php.ini
   * @var boolean
   */
  var $magic_quotes = false;
  
  /**
   * Constructor
   * @param string The path the the file
   * @throws Swift_FileException If the file cannot be found
   */
  function Swift_File($path)
  {
    $this->setPath($path);
    $this->magic_quotes = get_magic_quotes_runtime();
  }
  /**
   * Set the path to the file
   * @param string The path to the file
   * @throws Swift_FileException If the file cannot be found
   */
  function setPath($path)
  {
    if (!file_exists($path))
    {
      Swift_Errors::trigger(new Swift_FileException("No such file '" . $path ."'"));
      return;
    }
    $this->handle = null;
    $this->path = $path;
    $this->name = null;
    $this->name = $this->getFileName();
  }
  /**
   * Get the path to the file
   * @return string
   */
  function getPath()
  {
    return $this->path;
  }
  /**
   * Get the name of the file without it's full path
   * @return string
   */
  function getFileName()
  {
    if ($this->name !== null)
    {
      return $this->name;
    }
    else
    {
      return basename($this->getPath());
    }
  }
  /**
   * Establish an open file handle on the file if the file is not yet opened
   * @throws Swift_FileException If the file cannot be opened for reading
   */
  function createHandle()
  {
    if ($this->handle === null)
    {
      if (!$this->handle = fopen($this->path, "rb"))
      {
        Swift_Errors::trigger(new Swift_FileException(
          "Unable to open file '" . $this->path . " for reading.  Check the file permissions."));
        return;
      }
    }
  }
  /**
   * Check if the pointer as at the end of the file
   * @return boolean
   * @throws Swift_FileException If the file cannot be read
   */
  function EOF()
  {
    $this->createHandle();
    return feof($this->handle);
  }
  /**
   * Get a single byte from the file
   * Returns false past EOF
   * @return string
   * @throws Swift_FileException If the file cannot be read
   */
  function getByte()
  {
    $this->createHandle();
    return $this->read(1);
  }
  /**
   * Read one full line from the file including the line ending
   * Returns false past EOF
   * @return string
   * @throws Swift_FileException If the file cannot be read
   */
  function readln()
  {
    set_magic_quotes_runtime(0);
    $this->createHandle();
    if (!$this->EOF())
    {
      $ret = fgets($this->handle);
    }
    else $ret = false;
    
    set_magic_quotes_runtime($this->magic_quotes);
    
    return $ret;
  }
  /**
   * Get the entire file contents as a string
   * @return string
   * @throws Swift_FileException If the file cannot be read
   */
  function readFull()
  {
    $ret = "";
    set_magic_quotes_runtime(0);
    while (false !== $chunk = $this->read(8192, false)) $ret .= $chunk;
    set_magic_quotes_runtime($this->magic_quotes);
    return $ret;
  }
  /**
   * Read a given number of bytes from the file
   * Returns false past EOF
   * @return string
   * @throws Swift_FileException If the file cannot be read
   */
  function read($bytes, $unquote=true)
  {
    if ($unquote) set_magic_quotes_runtime(0);
    $this->createHandle();
    if (!$this->EOF())
    {
      $ret = fread($this->handle, $bytes);
    }
    else $ret = false;
    
    if ($unquote) set_magic_quotes_runtime($this->magic_quotes);
    
    return $ret;
  }
  /**
   * Get the size of the file in bytes
   * @return int
   */
  function length()
  {
    return filesize($this->path);
  }
  /**
   * Close the open handle on the file
   * @throws Swift_FileException If the file cannot be read
   */
  function close()
  {
    $this->createHandle();
    fclose($this->handle);
    $this->handle = null;
  }
  /**
   * Reset the file pointer back to zero
   */
  function reset()
  {
    $this->createHandle();
    fseek($this->handle, 0);
  }
}
