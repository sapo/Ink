<?php

/**
 * Swift Mailer Connect Event
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Events");

/**
 * Generated every time Swift connects with a MTA
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Events_ConnectEvent extends Swift_Events
{
  /**
   * A reference to the connection object
   * @var Swift_Connection
   */
  var $connection = null;
  
  /**
   * Constructor
   * @param Swift_Connection The new connection
   */
  function Swift_Events_ConnectEvent(&$connection)
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
