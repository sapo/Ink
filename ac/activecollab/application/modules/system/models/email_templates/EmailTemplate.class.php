<?php

  /**
   * EmailTemplate class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class EmailTemplate extends BaseEmailTemplate {
    
    /**
     * Caches subject values for locales
     *
     * @var array
     */
    var $subject_values = array();
    
    /**
     * Cached body values for locale
     *
     * @var string
     */
    var $body_values = array();
    
    /**
     * Return template subject
     *
     * @param string $language
     * @return string
     */
    function getSubject($language = null) {
    	if($language === null) {
    	  return parent::getSubject();
    	} else {
    	  if(!isset($this->subject_values[$language])) {
    	    $this->readLocaleProperties($language);
    	  } // if
    	  return isset($this->subject_values[$language]) ? $this->subject_values[$language] : parent::getSubject();
    	} // if
    } // getSubject
    
    /**
     * Return body value based on a language
     *
     * @param string $language
     * @return string
     */
    function getBody($language = null) {
    	if($language === null) {
    	  return parent::getBody($language);
    	} else {
    	  if(!isset($this->body_values[$language])) {
    	    $this->readLocaleProperties($language);
    	  } // if
    	  return isset($this->body_values[$language]) ? $this->body_values[$language] : parent::getBody();
    	} // if
    } // getBody
    
    /**
     * Read locale properties
     *
     * @param string $language
     * @return null
     */
    function readLocaleProperties($language) {
    	$row = db_execute_one('SELECT subject, body FROM ' . TABLE_PREFIX . 'email_template_translations WHERE name = ? AND module = ? AND locale = ?', $this->getName(), $this->getModule(), $language);
    	if(is_array($row)) {
    	  $this->subject_values[$language] = $row['subject'];
    	  $this->body_values[$language]    = $row['body'];
    	} else {
    	  return false;
    	} // if
    } // readLocaleProperties
    
    /**
     * write locale properties
     *
     * @param string $subject
     * @param string $body
     * @param string $locale
     * @return boolean
     */
    function writeLocaleProperties($subject, $body, $locale) {
    	$count = (integer) array_var(
    	  db_execute_one("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . 'email_template_translations WHERE name = ? AND module = ? AND locale = ?', $this->getName(), $this->getModule(), $locale), 
    	  'row_count'
    	);
    	
    	if($count) {
    	  return db_execute('UPDATE ' . TABLE_PREFIX . 'email_template_translations SET subject = ?, body = ? WHERE name = ? AND module = ? AND locale = ?', $subject, $body, $this->getName(), $this->getModule(), $locale);
    	} else {
    	  return db_execute('INSERT INTO ' . TABLE_PREFIX . 'email_template_translations (name, module, locale, subject, body) VALUES (?, ?, ?, ?, ?)', $this->getName(), $this->getModule(), $locale, $subject, $body);
    	} // if
    } // writeLocaleProperties
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return template URL
     *
     * @param void
     * @return string
     */
    function getUrl() {
    	return assemble_url('admin_settings_email_template', array(
    	  'module_name' => $this->getModule(),
        'template_name' => $this->getName(),
    	));
    } // getUrl
    
    /**
     * Return edit translation URL
     *
     * @param string $locale
     * @return string
     */
    function getEditUrl($locale = null) {
      $params = array(
        'module_name' => $this->getModule(),
        'template_name' => $this->getName(),
      );
      
      if($locale) {
        $params['locale'] = $locale;
      } // if
      
    	return assemble_url('admin_settings_email_template_edit', $params);
    } // getEditUrl
    
    /**
     * Set variables array
     *
     * @param mixed $value
     * @return mixed
     */
    function setVariables($value) {
    	if(is_array($value)) {
    	  return parent::setVariables(implode("\n", $value));
    	} else {
    	  return parent::setVariables($value);
    	} // if
    } // setVariables
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Prepare validation errors
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
    	if(!$this->validatePresenceOf('name')) {
    	  $errors->addError(lang('Template name is required'), 'name');
    	} // if
    	
    	if(!$this->validatePresenceOf('module')) {
    	  $errors->addError(lang('Module name is required'), 'module');
    	} // if
    	
    	if(!$this->validatePresenceOf('subject')) {
    	  $errors->addError(lang('Template subject is required'), 'subject');
    	} // if
    	
    	if(!$this->validatePresenceOf('body')) {
    	  $errors->addError(lang('Template body is required'), 'body');
    	} // if
    	
    	if($this->getName() && $this->getModule() && !$this->validateUniquenessOf('name', 'module')) {
    	  $errors->addError(lang('Template name needs to be unique on module level'));
    	} // if
    } // validate
  
  }

?>