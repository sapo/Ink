<?php

  /**
   * Feed item class
   * 
   * Objects of this class represent single feed item. Required properties are 
   * title, link, description and publication date. Author is optional
   * 
   * @package angie.library.feed
   */
  class FeedItem extends AngieObject {
    
    /**
     * Item title
     *
     * @var string
     */
    var $title;
    
    /**
     * Item link
     *
     * @var string
     */
    var $link;
    
    /**
     * Item description
     *
     * @var string
     */
    var $description;
    
    /**
     * Publication date
     *
     * @var DateTimeValue
     */
    var $publication_date;
    
    /**
     * Item author
     *
     * @var FeedAuthor
     */
    var $author;
    
    /**
     * Item ID
     *
     * @var mixed
     */
    var $id;
  
    /**
     * Constructor
     * 
     * Construct the feed item and set internal properties. Title, link, description and publication dates are required 
     * values
     *
     * @param string $title
     * @param string $link
     * @param string $description
     * @param DateTimeValue $publication_date
     * @return FeedItem
     */
    function __construct($title, $link, $description, $publication_date) {
      $this->setTitle($title);
      $this->setLink($link);
      $this->setDescription($description);
      $this->setPublicationDate($publication_date);
    } // __construct
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get title
     *
     * @param null
     * @return string
     */
    function getTitle() {
      return $this->title;
    } // getTitle
    
    /**
     * Set title value
     *
     * @param string $value
     * @return null
     */
    function setTitle($value) {
      $this->title = $value;
    } // setTitle
    
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
     */
    function setLink($value) {
      $this->link = $value;
    } // setLink
    
    /**
     * Get description
     *
     * @param null
     * @return string
     */
    function getDescription() {
      return $this->description;
    } // getDescription
    
    /**
     * Set description value
     *
     * @param string $value
     * @return null
     */
    function setDescription($value) {
      $this->description = $value;
    } // setDescription
    
    /**
     * Get publication_date
     *
     * @param null
     * @return DateTimeValue
     */
    function getPublicationDate() {
      return $this->publication_date;
    } // getPublicationDate
    
    /**
     * Set publication_date value
     *
     * @param DateTimeValue $value
     * @return null
     */
    function setPublicationDate($value) {
      $this->publication_date = $value;
    } // setPublicationDate
    
    /**
     * Get author
     *
     * @param null
     * @return FeedAuthor
     */
    function getAuthor() {
      return $this->author;
    } // getAuthor
    
    /**
     * Set author value
     *
     * @param FeedAuthor $value
     * @return null
     */
    function setAuthor($value) {
      $this->author = $value;
    } // setAuthor
    
    /**
     * Get ID
     *
     * @param null
     * @return string
     */
    function getId() {
      return $this->id;
    } // getId
    
    /**
     * Set ID value
     *
     * @param string $value
     * @return null
     */
    function setId($value) {
      $this->id = $value;
    } // setId
  
  } // FeedItem

?>