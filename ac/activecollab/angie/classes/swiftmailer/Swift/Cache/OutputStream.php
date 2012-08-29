<?php

/**
 * Swift Mailer Output stream to read bytes from cached data
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Cache
 * @license GNU Lesser General Public License
 */

/**
 * The wraps the streaming functionality of the cache
 * @package Swift_Cache
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Cache_OutputStream
{
  /**
   * The key to read in the actual cache
   * @var string
   */
  var $key;
  /**
   * The cache object to read
   * @var Swift_Cache
   */
  var $cache;
  
  /**
   * Ctor.
   * @param Swift_Cache The cache to read from
   * @param string The key for the cached data
   */
  function Swift_Cache_OutputStream(&$cache, $key)
  {
    $this->cache =& $cache;
    $this->key = $key;
  }
  /**
   * Read bytes from the cache and seek through the buffer
   * Returns false if EOF is reached
   * @param int The number of bytes to read (could be ignored)
   * @return string The read bytes
   */
  function read($size=null)
  {
    return $this->cache->read($this->key, $size);
  }
  /**
   * Read the entire cached data as one string
   * @return string
   */
  function readFull()
  {
    $ret = "";
    while (false !== $bytes = $this->read())
      $ret .= $bytes;
    return $ret;
  }
}
