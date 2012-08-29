<?php

/**
 * Swift Mailer disk runtime cache
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Cache
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Cache");

$_SWIFT_FILECACHE_SAVE_PATH_ = "/tmp";

/**
 * Caches data in files on disk - this is the best approach if possible
 * @package Swift_Cache
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Cache_Disk extends Swift_Cache
{
  /**
   * Open file handles
   * @var array
   */
  var $open = array();
  /**
   * The prefix to prepend to files
   * @var string
   */
  var $prefix;
  
  /**
   * Ctor
   */
  function Swift_Cache_Disk()
  {
     $this->prefix = md5(uniqid(microtime(), true));
     if(PHP_VERSION < 5) register_shutdown_function(array(&$this, "__destruct"));
  }
  /**
   * Set the save path of the disk - this is a global setting and called statically!
   * @param string The path to a writable directory
   */
  function setSavePath($path="/tmp")
  {
    $GLOBALS["_SWIFT_FILECACHE_SAVE_PATH_"] = realpath($path);
  }
  /**
   * Write data to the cache
   * @param string The cache key
   * @param string The data to write
   */
  function write($key, $data)
  {
    $handle = @fopen($GLOBALS["_SWIFT_FILECACHE_SAVE_PATH_"] . "/" . $this->prefix . $key, "ab");
    if (false === $handle || false === fwrite($handle, $data))
    {
      Swift_ClassLoader::load("Swift_FileException");
      Swift_ClassLoader::load("Swift_Errors");
      Swift_Errors::trigger(new Swift_FileException("Disk Caching failed.  Tried to write to file at [" .
        $GLOBALS["_SWIFT_FILECACHE_SAVE_PATH_"] . "/" . $this->prefix . $key . "] but failed.  Check the permissions, or don't use disk caching."));
      return;
    }
    fclose($handle);
  }
  /**
   * Clear the cached data (unlink)
   * @param string The cache key
   */
  function clear($key)
  {
    @unlink($GLOBALS["_SWIFT_FILECACHE_SAVE_PATH_"] . "/" . $this->prefix . $key);
  }
  /**
   * Check if data is cached for $key
   * @param string The cache key
   * @return boolean
   */
  function has($key)
  {
    return file_exists($GLOBALS["_SWIFT_FILECACHE_SAVE_PATH_"] . "/" . $this->prefix . $key);
  }
  /**
   * Read data from the cache for $key
   * @param string The cache key
   * @param int The number of bytes to read
   * @return string
   */
  function read($key, $size=null)
  {
    if ($size === null) $size = 8190;
    if (!$this->has($key)) return false;
    
    if (!isset($this->open[$key]))
    {
      $this->open[$key] = fopen($GLOBALS["_SWIFT_FILECACHE_SAVE_PATH_"] . "/" . $this->prefix . $key, "rb");
    }
    if (feof($this->open[$key]))
    {
      fclose($this->open[$key]);
      unset($this->open[$key]);
      return false;
    }
    $ret = fread($this->open[$key], $size);
    if ($ret !== false)
    {
      return $ret;
    }
    else
    {
      fclose($this->open[$key]);
      unset($this->open[$key]);
      return false;
    }
  }
  /**
   * Dtor.
   * Clear out cached data at end of script execution or cache destruction
   */
  function __destruct()
  {
    $list = glob($GLOBALS["_SWIFT_FILECACHE_SAVE_PATH_"] . "/" . $this->prefix . "*");
    foreach ((array)$list as $f)
    {
      @unlink($f);
    }
  }
}
