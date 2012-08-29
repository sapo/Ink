<?php

/**
 * Swift Mailer MySQL Resultset Iterator
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */


/**
 * Swift Mailer MySQL Resultset Iterator.
 * Iterates over MySQL Resultset from mysql_query().
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Iterator_MySQLResult
{
  /**
   * The MySQL resource.
   * @var resource
   */
  var $resultSet;
  /**
   * The current row (array) in the resultset.
   * @var array
   */
  var $currentRow = array(null, null);
  /**
   * The current array position.
   * @var int
   */
  var $pos = -1;
  /**
   * The total number of rows in the resultset.
   * @var int
   */
  var $numRows = 0;
  
  /**
   * Ctor.
   * @param resource The resultset iterate over.
   */
  function Swift_Iterator_MySQLResult($rs)
  {
    $this->resultSet = $rs;
    $this->numRows = mysql_num_rows($rs);
  }
  /**
   * Get the resultset.
   * @return resource
   */
  function getResultSet()
  {
    return $this->resultSet;
  }
  /**
   * Returns true if there is a value after the current one.
   * @return boolean
   */
  function hasNext()
  {
    return (($this->pos + 1) < $this->numRows);
  }
  /**
   * Moves to the next array element if possible.
   * @return boolean
   */
  function next()
  {
    if ($this->hasNext())
    {
      $this->currentRow = mysql_fetch_array($this->resultSet);
      $this->pos++;
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
    if ($pos >= 0 && $pos < $this->numRows)
    {
      mysql_data_seek($this->resultSet, $pos);
      $this->currentRow = mysql_fetch_array($this->resultSet);
      mysql_data_seek($this->resultSet, $pos);
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
    $row = $this->currentRow;
    if ($row[0] !== null)
      return new Swift_Address($row[0], isset($row[1]) ? $row[1] : null);
    else
      return null;
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
