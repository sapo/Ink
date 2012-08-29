<?php

/**
 * Swift Mailer Recipient List Container
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";
Swift_ClassLoader::load("Swift_Address");
Swift_ClassLoader::load("Swift_Iterator_Array");

/**
 * Swift's Recipient List container.  Contains To, Cc, Bcc
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_RecipientList extends Swift_AddressContainer
{
  /**
   * The recipients in the To: header
   * @var array
   */
  var $to = array();
  /**
   * The recipients in the Cc: header
   * @var array
   */
  var $cc = array();
  /**
   * The recipients in the Bcc: header
   * @var array
   */
  var $bcc = array();
  /**
   * Iterators to use when getting lists back out.
   * If any iterators are present here, their relevant "addXX()" methods will be useless.
   * As per the last note, any iterators need to be pre-configured before Swift::send() is called.
   * @var array,Swift_Iterator
   */
  var $iterators = array("to" => null, "cc" => null, "bcc" => null);
  
  /**
   * Add a recipient.
   * @param string The address
   * @param string The name
   * @param string The field (to, cc or bcc)
   */
  function add($address, $name="", $where="to")
  {
    if (is_a($address, "Swift_Address"))
    {
      $address_str = trim(strtolower($address->getAddress()));
    }
    elseif (is_array($address))
    {
      foreach ($address as $a) $this->add($a, $name, $where);
      return;
    }
    else
    {
      $address_str = (string) $address;
      $address_str = trim(strtolower($address_str));
      $address =& new Swift_Address($address_str, $name);
    }
    
    if (in_array($where, array("to", "cc", "bcc")))
    {
      $container =& $this->$where;
      $container[$address_str] =& $address;
    }
  }
  /**
   * Remove a recipient.
   * @param string The address
   * @param string The field (to, cc or bcc)
   */
  function remove($address, $where="to")
  {
    if (is_a($address, "Swift_Address"))
    {
      $key = trim(strtolower($address->getAddress()));
    }
    else $key = trim(strtolower((string) $address));
    
    if (in_array($where, array("to", "cc", "bcc")))
    {
      if (array_key_exists($key, $this->$where)) unset($this->{$where}[$key]);
    }
  }
  /**
   * Get an iterator object for all the recipients in the given field.
   * @param string The field name (to, cc or bcc)
   * @return Swift_Iterator
   */
  function &getIterator($where)
  {
    $null = null;
    if (!empty($this->iterators[$where]))
    {
      return $this->iterators[$where];
    }
    elseif (in_array($where, array("to", "cc", "bcc")))
    {
      $it =& new Swift_Iterator_Array($this->$where);
      return $it;
    }
    return $null;
  }
  /**
   * Override the loading of the default iterator (Swift_ArrayIterator) and use the one given here.
   * @param Swift_Iterator The iterator to use.  It must be populated already.
   */
  function setIterator(&$it, $where)
  {
    if (in_array($where, array("to", "cc", "bcc")))
    {
      $this->iterators[$where] =& $it;
    }
  }
  /**
   * Add a To: recipient
   * @param mixed The address to add.  Can be a string or Swift_Address
   * @param string The personal name, optional
   */
  function addTo($address, $name=null)
  {
    $this->add($address, $name, "to");
  }
  /**
   * Get an array of addresses in the To: field
   * The array contains Swift_Address objects
   * @return array
   */
  function &getTo()
  {
    return $this->to;
  }
  /**
   * Remove a To: recipient from the list
   * @param mixed The address to remove.  Can be Swift_Address or a string
   */
  function removeTo($address)
  {
    $this->remove($address, "to");
  }
  /**
   * Empty all To: addresses
   */
  function flushTo()
  {
    $this->to = null;
    $this->to = array();
  }
  /**
   * Add a Cc: recipient
   * @param mixed The address to add.  Can be a string or Swift_Address
   * @param string The personal name, optional
   */
  function addCc($address, $name=null)
  {
    $this->add($address, $name, "cc");
  }
  /**
   * Get an array of addresses in the Cc: field
   * The array contains Swift_Address objects
   * @return array
   */
  function &getCc()
  {
    return $this->cc;
  }
  /**
   * Remove a Cc: recipient from the list
   * @param mixed The address to remove.  Can be Swift_Address or a string
   */
  function removeCc($address)
  {
    $this->remove($address, "cc");
  }
  /**
   * Empty all Cc: addresses
   */
  function flushCc()
  {
    $this->cc = null;
    $this->cc = array();
  }
  /**
   * Add a Bcc: recipient
   * @param mixed The address to add.  Can be a string or Swift_Address
   * @param string The personal name, optional
   */
  function addBcc($address, $name=null)
  {
    $this->add($address, $name, "bcc");
  }
  /**
   * Get an array of addresses in the Bcc: field
   * The array contains Swift_Address objects
   * @return array
   */
  function &getBcc()
  {
    return $this->bcc;
  }
  /**
   * Remove a Bcc: recipient from the list
   * @param mixed The address to remove.  Can be Swift_Address or a string
   */
  function removeBcc($address)
  {
    $this->remove($address, "bcc");
  }
  /**
   * Empty all Bcc: addresses
   */
  function flushBcc()
  {
    $this->bcc = null;
    $this->bcc = array();
  }
  /**
   * Empty the entire list
   */
  function flush()
  {
    $this->flushTo();
    $this->flushCc();
    $this->flushBcc();
  }
}
