<?php
  class MailboxManagerEmail extends AngieObject {
    
    /**
     * Message id
     * 
     * @var integer
     */
    var $id;
    
    /**
     * Message Subject
     *
     * @var string
     */
    var $subject;
    
    /**
     * Message Date
     *
     * @var string
     */
    var $date;
    
    /**
     * Message size
     *
     * @var string
     */    
    var $size;
    
    /**
     * Body parts
     * 
     * @var array
     */
    var $bodies;
    
    /**
     * Body alternatives
     * 
     * @var array
     */
    var $alternatives;
    
    /**
     * Attachments
     *
     * @var array
     */
    var $attachments;
    
    /**
     * Addresses
     * 
     * @var array
     */
    var $addresses;
    
    /**
     * Email headers
     *
     * @var string
     */
    var $headers;
           
    /**
     * Constructor method
     *
     */
    function __construct() {
      
    } // __construct
    
    /**
     * Set email subject
     *
     * @param string $subject
     */
    function setSubject($subject) {
      $this->subject = trim($subject);
    } // setSubject
    
    /**
     * Get email subject
     *
     */
    function getSubject() {
      return $this->subject;
    } // getSubject
    
    /**
     * Set the email time stamp
     *
     */
    function setDate($date_timestamp) {
      $this->date = $date_timestamp;
    } // setDate
    
    /**
     * Retrieves date timestamp
     *
     */
    function getDate() {
      return $this->date;
    } // getDate
    
    /**
     * Sets the email size
     *
     * @param integer $email_size
     */
    function setSize($email_size) {
      $this->size = (integer) $email_size;
    } // setSize
    
    /**
     * Retrieve email size
     *
     */
    function getSize() {
      return $this->size;
    } // getSize
    
    /**
     * Set email id
     *
     * @param string $id
     */
    function setId($id) {
      $this->id = $id;
    } // setId
    
    /**
     * Get email id
     *
     * @return string
     */
    function getId() {
      return $this->id;
    } // getId
    
    /**
     * Set email headers
     *
     * @param string $headers
     */
    function setHeaders($headers) {
      $this->headers = $headers;
    } // setHeaders
    
    /**
     * Return email headers
     *
     * @return string
     */
    function getHeaders() {
      return $this->headers;
    } // getHeaders
    
    /**
     * Add recipients
     *
     * @param string $email_address
     * @param string $name
     * @param string $group
     */
    function addAddress($email_address, $name = null, $group = 'to') {
  		$this->addresses[$group][] = array(
  		  'name'  => $name,
  		  'email' => $email_address
  		);
    } // addRecipient
    
    /**
     * Retrieve recipient data
     *
     * @param string $group
     * @param integer $id
     * @return array
     */
    function getAddress($group = 'to', $id = 0) {
      return array_var(array_var($this->addresses, $group), $id);
    } // getAddress
    
    /**
     * Add Body Part
     *
     * @param string $part_id
     * @param string $content_type
     * @param string $content
     * @return boolean
     */
    function addBody($part_id, $content_type, $content = null) {
      $this->bodies[] = array(
        'part_id'       => $part_id,
        'content_type'  => $content_type,
        'content'       => trim($content)
      );
      return true;
    } // addBodyPart
    
    /**
     * Add alternative to body
     *
     * @param string $part_id
     * @param string $alternative_to
     * @param string $content_type
     * @param string $content
     * @return boolean
     */
    function addAlternative($part_id, $alternative_to, $content_type, $content = null) {
      $this->alternatives[] = array(
        'part_id'      => $part_id,
        'original'     => $alternative_to,
        'content_type' => $content_type,
        'content'      => trim($content),
      );
      return true;
    } // addAlternative
    
    /**
     * Add attachment
     *
     * @param integer $part_id
     * @param string $content_type
     * @param string $filename
     * @param string $path
     * @param integer $size
     * @return boolean
     */
    function addAttachment($part_id, $content_type, $filename, $path, $size=null) {
      $this->attachments[] = array(
        'id'            => $part_id,
        'content_type'  => $content_type,
        'filename'      => $filename,
        'path'          => $path,
        'size'          => $size,
      );
      return true;
    } // addAttachment
    
    
    /**
     * Return email attachments
     *
     * @return array
     */
    function getAttachments() {
      return $this->attachments;  
    } // getAttachments();
    
    /**
     * Returns processed bodies
     * 
     * @param string $content_type
     * @return string
     */
    function getBody($content_type) {
      $bodies = $this->getBodies($content_type);
      if (!is_foreachable($bodies)) {
        return null;  
      } // if
      
      for ($x = 0; $x < count($bodies); $x++) {
        $bodies[$x] = $bodies[$x]['content'];
      } // for
           
      $processed = implode($prefered_content_type == 'text/html' ? '<br />' : "\n", $bodies);
      return trim($processed);
    } // getProcessedBody
    
    /**
     * function find alternative for body
     *
     * @param string $body_id
     * @param string $prefered_content_type
     * @return string
     */
    function getAlternative($body_id, $prefered_content_type) {
      for ($x = 0; $x < count($this->alternatives); $x++) {
        if (array_var($this->alternatives[$x], 'original') == $body_id) {
          if (strcasecmp(array_var($this->alternatives[$x], 'content_type'), $prefered_content_type) == 0) {
            return $this->alternatives[$x];
          } // if
        } // if
      } // for
      return false;
    }
    
    /**
     * Get bodies with prefered content
     *
     * @param string $content_type
     */
    function getBodies($content_type) {
      $bodies = array();
      
      if (!is_foreachable($this->bodies)) {
        return false;
      } // if
      
      foreach ($this->bodies as $body) {
        if (strtoupper($content_type) == strtoupper(array_var($body,'content_type'))) {
          $bodies[] = $body;
        } else {
          if ($alternative = $this->getAlternative(array_var($body, 'part_id'), $content_type)) {
            $bodies[] = $alternative;
          } // if
        } // if
      } // if
      return $bodies;
    } // getBodies
    
    /**
     * Returns email client name (if it's known)
     *
     * @return string
     */
    function getEmailClient() {
      //pre_var_dump($this->getHeaders());
      $headers = $this->getHeaders();
      
      // check if mailer is gmail
      if (strpos($headers, 'X-Mailer: Apple Mail') !== false) {
        return MM_EMAIL_CLIENT_APPLE_MAIL;
      } // if
      
      if (preg_match('/Message\-ID\:\ \<(.*?)@mail.gmail.com\>/is', $headers)) {
        return MM_EMAIL_CLIENT_GMAIL;
      } // if
      
      return false;
    } // getEmailClient   
  } // MailboxManagerEmail
  

?>