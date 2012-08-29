<?php

  /**
   * Anonymous user class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class AnonymousUser extends AngieObject {
    
    /**
     * Users name
     * 
     * @var string
     */
    var $name;
    
    /**
     * Users email address
     * 
     * @var string
     */
    var $email;
    
    /**
     * Construct anonymous user instance
     *
     * @param string $name
     * @param string $email
     * @return AnonymousUser
     */
    function __construct($name, $email) {
      $this->email = $email;
      $this->name = $name;
    } // __construct
    
    // ---------------------------------------------------
    //  User class compatibility methods
    // ---------------------------------------------------
    
    /**
     * Return ID
     *
     * @param void
     * @return integer
     */
    function getId() {
      return 0;
    } // getId
    
    /**
     * Return display name
     *
     * @param boolean $short
     * @return string
     */
    function getDisplayName($short = false) {
      $name = $this->getName();
      if($short) {
        $pieces = explode(' ', $name);
        if(count($pieces) == 2) {
          return $pieces[0] . ' ' . substr_utf($pieces[1], 0, 1) . '.';
        } else {
          return $name;
        }
      } else {
        return $name;
      } // if
    } // getDisplayName
    
    /**
     * Return view URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
      return 'mailto:' . $this->getEmail();
    } // getViewUrl
    
    /**
     * Return user avatar URL
     *
     * @param boolean $large
     * @return string
     */
    function getAvatarUrl($large = false) {
      $size = $large ? '40x40' : '16x16';
      return ROOT_URL . "/avatars/default.$size.gif";
    } // getAvatarUrl
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get value of name
     *
     * @param void
     * @return string
     */
    function getName() {
      return $this->name;
    } // getName
    
    /**
     * Get value of email
     *
     * @param void
     * @return string
     */
    function getEmail() {
      return $this->email;
    } // getEmail
    
  } // AnonymousUser

?>