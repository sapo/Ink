<?php

/**
 * Swift Mailer Disconnect Event
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Events");

/**
 * Generated every time Swift disconnects from a MTA
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Events_DisconnectEvent extends Swift_Events
{
  /**
   * A reference to the connection object
   * @var Swift_Connection
   */
  var $connection = null;
  
  /**
   * Constructor
   * @param Swift_Connection The dead connection
   */
  function Swift_Events_DisconnectEvent(&$connection)
  {
    $this->connection =& $connection;
  }
  /**
   * Get the connection object
   * @return Swift_Connection
   */
  function &getConnection()
  {
    return $this->connection;
  }
}
