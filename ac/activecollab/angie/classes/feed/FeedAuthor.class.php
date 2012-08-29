<?php

  /**
   * Feed author class
   * 
   * Object of FeedAuthor describes single feed entry author wutg name, email 
   * and homepage properties. Name and email are required
   * 
   * @package angie.library.feed
   */
  class FeedAuthor extends AngieObject {
    
    /**
     * Author name
     *
     * @var string
     */
    var $name;
    
    /**
     * Author email address
     *
     * @var string
     */
    var $email;
    
    /**
     * Author homepage URL
     *
     * @var string
     */
    var $link;
  
    /**
     * Constructor
     *
     * @param string $name
     * @param string $email
     * @param string $link
     * @return FeedAuthor
     * @throws InvalidParamError if $email or $link values are present but they are not valid
     */
    function __construct($name, $email, $link = null) {
      $this->setName($name);
      $this->setEmail($email);
      $this->setLink($link);
    } // __construct
    
    /**
     * Check if author object is empty
     * 
     * This function will return true if values of required fields are empty. Required fields are name and email. Link is 
     * optional
     *
     * @param void
     * @return boolean
     */
    function isEmpty() {
      return (trim($this->name) == '') && (trim($this->email) == '');
    } // isEmpty
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get name
     *
     * @param null
     * @return string
     */
    function getName() {
      return $this->name;
    } // getName
    
    /**
     * Set name value
     *
     * @param string $value
     * @return null
     */
    function setName($value) {
      $this->name = $value;
    } // setName
    
    /**
     * Get email
     *
     * @param null
     * @return string
     */
    function getEmail() {
      return $this->email;
    } // getEmail
    
    /**
     * Set email value
     *
     * @param string $value
     * @return null
     * @throws InvalidParamError
     */
    function setEmail($value) {
      $this->email = $value;
    } // setEmail
    
    /**
     * Get link
     *
     * @param null
     * @return string
     */
    function getLink() {
      return $this->link;
    } // getLink
    
    /**
     * Set link value
     *
     * @param string $value
     * @return null
     * @throws InvalidParamError
     */
    function setLink($value) {
      $this->link = $value;
    } // setLink
  
  } // FeedAuthor

?>