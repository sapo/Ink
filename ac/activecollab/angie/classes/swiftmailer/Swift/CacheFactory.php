<?php

/**
 * Swift Mailer Cache Factory class
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Cache
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";

$GLOBALS["_SWIFT_CACHE_CLASS_"] = "Swift_Cache_Memory";

/**
 * Makes instances of the cache the user has defined
 * @package Swift_Cache
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_CacheFactory
{
  /**
   * Set the name of the class which is supposed to be used
   * This also includes the file
   * @param string The class name
   */
  function setClassName($name)
  {
    Swift_ClassLoader::load($name);
    $GLOBALS["_SWIFT_CACHE_CLASS_"] = $name;
  }
  /**
   * Return a new instance of the cache object
   * @return Swift_Cache
   */
  function &getCache()
  {
    $className = $GLOBALS["_SWIFT_CACHE_CLASS_"];
    Swift_ClassLoader::load($className);
    $instance =& new $className();
    return $instance;
  }
}
