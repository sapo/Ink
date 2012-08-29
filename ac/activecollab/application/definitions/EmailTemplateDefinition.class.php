<?php

  /**
   * Email templates definition
   *
   * @package activeCollab
   */
  class EmailTemplateDefinition extends AngieObject {
    
    /**
     * Email subject
     *
     * @var string
     */
    var $subject;
    
    /**
     * Email body
     *
     * @var string
     */
    var $body;
    
    /**
     * Module name
     *
     * @var string
     */
    var $module;
    
    /**
     * Template variables
     *
     * @var array
     */
    var $variables;
    
    /**
     * Construct email tempate definition
     *
     * @param string $subject
     * @param string $body
     * @param string $module
     * @param array $variables
     * @return EmailTemplateDefinition
     */
    function __construct($subject, $body, $module, $variables = null) {
    	$this->subject = $subject;
    	$this->body = $body;
    	$this->module = $module;
    	$this->variables = $variables;
    } // __construct
    
  }

?>