<?php

/**
 * Swift Mailer Message Decorating Plugin.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Plugin
 * @subpackage Decorator
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Plugin_Decorator_Replacements");

/**
 * Swift Decorator Plugin.
 * Allows messages to be slightly different for each recipient.
 * @package Swift_Plugin
 * @subpackage Decorator
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Plugin_Decorator extends Swift_Events_Listener
{
  /**
   * The replacements object.
   * @var Swift_Plugin_Decorator_Replacements
   */
  var $replacements;
  /**
   * Temporary storage so we can restore changes we make.
   * @var array
   */
  var $store;
  /**
   * A list of allowed mime types to replace bodies for.
   * @var array
   */
  var $permittedTypes = array("text/plain" => 1, "text/html" => 1);
  /**
   * True if values in the headers can be replaced
   * @var boolean
   */
  var $permittedInHeaders = true;
  
  /**
   * Ctor.
   * @param mixed Replacements as a 2-d array or Swift_Plugin_Decorator_Replacements instance.
   */
  function Swift_Plugin_Decorator($replacements=null)
  {
    $this->setReplacements($replacements);
  }
  /**
   * Enable of disable the ability to replace values in the headers
   * @param boolean
   */
  function setPermittedInHeaders($bool)
  {
    $this->permittedInHeaders = (bool) $bool;
  }
  /**
   * Check if replacements in headers are allowed.
   * @return boolean
   */
  function getPermittedInHeaders()
  {
    return $this->permittedInHeaders;
  }
  /**
   * Add a mime type to the list of permitted type to replace values in the body.
   * @param string The mime type (e.g. text/plain)
   */
  function addPermittedType($type)
  {
    $type = strtolower($type);
    $this->permittedTypes[$type] = 1;
  }
  /**
   * Remove the ability to replace values in the body of the given mime type
   * @param string The mime type
   */
  function removePermittedType($type)
  {
    unset($this->permittedTypes[$type]);
  }
  /**
   * Get the list of mime types for which the body can be changed.
   * @return array
   */
  function getPermittedTypes()
  {
    return array_keys($this->permittedTypes);
  }
  /**
   * Check if the body can be replaced in the given mime type.
   * @param string The mime type
   * @return boolean
   */
  function isPermittedType($type)
  {
    return array_key_exists(strtolower($type), $this->permittedTypes);
  }
  /**
   * Called just before Swift sends a message.
   * We perform operations on the message here.
   * @param Swift_Events_SendEvent The event object for sending a message
   */
  function beforeSendPerformed(&$e)
  {
    $message =& $e->getMessage();
    $recipients =& $e->getRecipients();
    $to = array_keys($recipients->getTo());
    if (count($to) > 0) $to = $to[0];
    else return;
    
    $replacements = (array)$this->replacements->getReplacementsFor($to);
    
    $this->store = array(
      "headers" => array(),
      "body" => false,
      "children" => array()
    );
    $this->recursiveReplace($message, $replacements, $this->store);
  }
  /**
   * Replace strings in the message searching through all the allowed sub-parts.
   * @param Swift_Message_Mime The message (or part)
   * @param array The list of replacements
   * @param array The array to cache original values into where needed
   */
  function recursiveReplace(&$mime, $replacements, &$store)
  {
    //Check headers
    if ($this->getPermittedInHeaders())
    {
      foreach ($mime->headers->getList() as $name => $value)
      {
        if (is_string($value) && ($replaced = $this->replace($replacements, $value)) != $value)
        {
          $mime->headers->set($name, $replaced);
          $store["headers"][$name] = array();
          $store["headers"][$name]["value"] = $value;
          $store["headers"][$name]["attributes"] = array();
        }
        foreach ($mime->headers->listAttributes($name) as $att_name => $att_value)
        {
          if (is_string($att_value)
            && ($att_replaced = $this->replace($replacements, $att_value)) != $att_value)
          {
            if (!isset($store["headers"][$name]))
            {
              $store["headers"][$name] = array("value" => false, "attributes" => array());
            }
            $mime->headers->setAttribute($name, $att_name, $att_replaced);
            $store["headers"][$name]["attributes"][$att_name] = $att_value;
          }
        }
      }
    }
    //Check body
    $body = $mime->getData();
    if ($this->isPermittedType($mime->getContentType())
      && is_string($body) && ($replaced = $this->replace($replacements, $body)) != $body)
    {
      $mime->setData($replaced);
      $store["body"] = $body;
    }
    //Check sub-parts
    foreach ($mime->listChildren() as $id)
    {
      $store["children"][$id] = array(
        "headers" => array(),
        "body" => false,
        "children" => array()
      );
      $child =& $mime->getChild($id);
      $this->recursiveReplace($child, $replacements, $store["children"][$id]);
    }
  }
  /**
   * Perform a str_replace() over the given value.
   * @param array The list of replacements as (search => replacement)
   * @param string The string to replace
   * @return string
   */
  function replace($replacements, $value)
  {
    return str_replace(array_keys($replacements), array_values($replacements), $value);
  }
  /**
   * Called just after Swift sends a message.
   * We restore the message here.
   * @param Swift_Events_SendEvent The event object for sending a message
   */
  function sendPerformed(&$e)
  {
    $message =& $e->getMessage();
    $this->recursiveRestore($message, $this->store);
    $this->store = null;
  }
  /**
   * Put the original values back in the message after it was modified before sending.
   * @param Swift_Message_Mime The message (or part)
   * @param array The location of the stored values
   */
  function recursiveRestore(&$mime, &$store)
  {
    //Restore headers
    foreach ($store["headers"] as $name => $array)
    {
      if ($array["value"] !== false) $mime->headers->set($name, $array["value"]);
      foreach ($array["attributes"] as $att_name => $att_value)
      {
        $mime->headers->setAttribute($name, $att_name, $att_value);
      }
    }
    //Restore body
    if ($store["body"] !== false)
    {
      $mime->setData($store["body"]);
    }
    //Restore children
    foreach ($store["children"] as $id => $child_store)
    {
      $child =& $mime->getChild($id);
      $this->recursiveRestore($child, $child_store);
    }
  }
  /**
   * Set the replacements as a 2-d array or an instance of Swift_Plugin_Decorator_Replacements.
   * @param mixed Array or Swift_Plugin_Decorator_Replacements
   */
  function setReplacements(&$replacements)
  {
    if ($replacements === null)
    {
      $r = array();
      $this->replacements =& new Swift_Plugin_Decorator_Replacements($r);
    }
    elseif (is_array($replacements))
    {
      $this->replacements =& new Swift_Plugin_Decorator_Replacements($replacements);
    }
    elseif (is_a($replacements, "Swift_Plugin_Decorator_Replacements"))
    {
      $this->replacements =& $replacements;
    }
    else
    {
      trigger_error(
        "Decorator replacements must be array or instance of Swift_Plugin_Decorator_Replacements.");
    }
  }
  /**
   * Get the replacements object.
   * @return Swift_Plugin_Decorator_Replacements
   */
  function &getReplacements()
  {
    return $this->replacements;
  }
}
