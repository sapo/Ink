<?php

/**
 * Swift Mailer Verbose Sending Plugin.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Plugin
 * @subpackage VerboseSending
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Plugin_VerboseSending_DefaultView");

/**
 * Verbose Sending plugin for Swift Mailer.
 * Displays "pass" or "fail" messages in realtime as the messages are sent.
 * @package Swift_Plugin
 * @subpackage VerboseSending
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Plugin_VerboseSending extends Swift_Events_Listener
{
  /**
   * The view layer which displays the results.
   * @var Swift_Plugin_VerboseSending_AbstractView
   */
  var $view;
  
  /**
   * Ctor.
   * @param Swift_Plugin_VerboseSending_AbstractView The view object to display the result
   */
  function Swift_Plugin_VerboseSending(&$view)
  {
    $this->setView($view);
  }
  /**
   * Part of the interface which is notified when a message has been sent.
   * @param Swift_Events_SendEvent
   */
  function sendPerformed(&$e)
  {
    $recipients =& $e->getRecipients();
    $failed = $e->getFailedRecipients();
    $it =& $recipients->getIterator("to");
    while ($it->hasNext())
    {
      $it->next();
      $address = $it->getValue();
      $pass = !in_array($address->getAddress(), $failed);
      $view =& $this->getView();
      $view->paintResult($address->getAddress(), $pass);
    }
    $it =& $recipients->getIterator("cc");
    while ($it->hasNext())
    {
      $it->next();
      $address = $it->getValue();
      $pass = !in_array($address->getAddress(), $failed);
      $view =& $this->getView();
      $view->paintResult($address->getAddress(), $pass);
    }
    $it =& $recipients->getIterator("cc");
    while ($it->hasNext())
    {
      $it->next();
      $address = $it->getValue();
      $pass = !in_array($address->getAddress(), $failed);
      $view =& $this->getView();
      $view->paintResult($address->getAddress(), $pass);
    }
  }
  /**
   * Set the View component to display results.
   * @param Swift_Plugin_VerboseSending_AbstractView The view object to display the result
   */
  function setView(&$view)
  {
    $this->view =& $view;
  }
  /**
   * Get the View component.
   * @return Swift_Plugin_VerboseSending_AbstractView
   */
  function &getView()
  {
    return $this->view;
  }
}
