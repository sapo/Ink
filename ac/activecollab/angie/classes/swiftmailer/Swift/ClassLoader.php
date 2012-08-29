<?php

/**
 * Swift Mailer Class Loader for includes
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */

if (!defined("SWIFT_ABS_PATH")) define("SWIFT_ABS_PATH", dirname(__FILE__) . "/..");

/**
 * Locates and includes class files
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_ClassLoader
{
  /**
   * Load a new class into memory (static)
   * @param string The name of the class, case SenSItivE
   */
  function load($name)
  {
    static $located = null;
    if (!$located) $located = array();
    
    if (in_array($name, $located) || class_exists($name))
      return;
    
    require_once SWIFT_ABS_PATH . "/" . str_replace("_", "/", $name) . ".php";
    $located[] = $name;
  }
}
