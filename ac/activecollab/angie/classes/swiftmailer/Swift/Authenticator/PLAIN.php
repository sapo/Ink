<?php

/**
 * Swift Mailer PLAIN Authenticator Mechanism
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Authenticator
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Authenticator");

/**
 * Swift PLAIN Authenticator
 * This form of authentication is unbelievably insecure since everything is done plain-text
 * @package Swift_Authenticator
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Authenticator_PLAIN extends Swift_Authenticator
{
  /**
   * Try to authenticate using the username and password
   * Returns false on failure
   * @param string The username
   * @param string The password
   * @param Swift The instance of Swift this authenticator is used in
   * @return boolean
   */
  function isAuthenticated($user, $pass, &$swift)
  {
    //The authorization string uses ascii null as a separator (See RFC 2554)
    $credentials = base64_encode($user . chr(0) . $user . chr(0) . $pass);
    Swift_ClassLoader::load("Swift_Errors");
    Swift_Errors::expect($e, "Swift_ConnectionException");
      $swift->command("AUTH PLAIN " . $credentials, 235);
    if ($e) {
      $swift->reset();
      return false;
    }
    Swift_Errors::clear("Swift_ConnectionException");
    return true;
  }
  /**
   * Return the name of the AUTH extension this is for
   * @return string
   */
  function getAuthExtensionName()
  {
    return "PLAIN";
  }
}
