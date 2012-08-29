<?php

/**
 * Swift Mailer Array Iterator Interface
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */


/**
 * Swift Array Iterator Interface
 * Iterates over a standard PHP array.
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Iterator_Array
{
  /**
   * All keys in this array.
   * @var array
   */
  var $keys;
  /**
   * All values in this array.
   * @var array
   */
  var $values;
  /**
   * The current array position.
   * @var int
   */
  var $pos = -1;
  
  /**
   * Ctor.
   * @param array The array to iterate over.
   */
  function Swift_Iterator_Array($input)
  {
    $input = (array) $input;
    $this->keys = array_keys($input);
    $this->values = array_values($input);
  }
  /**
   * Returns the original array.
   * @return array
   */
  function getArray()
  {
    $ret = array();
    foreach ($this->keys as $i => $k)
    {
      $ret[$k] = $this->values[$i];
    }
    return $ret;
  }
  /**
   * Returns true if there is a value after the current one.
   * @return boolean
   */
  function hasNext()
  {
    return array_key_exists($this->pos + 1, $this->keys);
  }
  /**
   * Moves to the next array element if possible.
   * @return boolean
   */
  function next()
  {
    if ($this->hasNext())
    {
      ++$this->pos;
      return true;
    }
    
    return false;
  }
  /**
   * Goes directly to the given element in the array if possible.
   * @param int Numeric position
   * @return boolean
   */
  function seekTo($pos)
  {
    if (array_key_exists($pos, $this->keys))
    {
      $this->pos = $pos;
      return true;
    }
    
    return false;
  }
  /**
   * Returns the value at the current position, or NULL otherwise.
   * @return mixed.
   */
  function getValue()
  {
    if (array_key_exists($this->pos, $this->values))
      return $this->values[$this->pos];
    else return null;
  }
  /**
   * Gets the current numeric position within the array.
   * @return int
   */
  function getPosition()
  {
    return $this->pos;
  }
}
