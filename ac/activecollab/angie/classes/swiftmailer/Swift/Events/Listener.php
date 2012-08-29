<?php

/**
 * Swift Mailer Event Interface
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Events_ListenerMapper");

/**
 * Used for identity only
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Events_Listener /*abstract*/
{
  function implementing($interface)
  {
    $method = Swift_Events_ListenerMapper::getNotifyMethod($interface);
    if ($method)
    {
      return method_exists($this, $method);
    }
  }
}
