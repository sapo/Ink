<?php

  /**
   * ConfigOption class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ConfigOption extends BaseConfigOption {
    
    /**
    * Get value
    *
    * @param null
    * @return mixed
    */
    function getValue() {
      $raw = parent::getValue();
      return $raw ? unserialize($raw) : null;
    } // getValue
    
    /**
    * Set value value
    *
    * @param mixed $value
    * @return null
    */
    function setValue($value) {
      return parent::setValue(serialize($value));
    } // setValue
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
    * Validate before save
    *
    * @param ValidationErrors $errors
    * @return null
    */
    function validate(&$errors) {
      if(!$this->validatePresenceOf('module')) {
        $errors->addError(lang('Module name is required'), 'module');
      } // if
      
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name')) {
          $errors->addError(lang('Option name must be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Option name is required'), 'name');
      } // if
      
      if(!$this->validatePresenceOf('type')) {
        $errors->addError(lang('Option type is required'), 'type');
      } // if
    } // validate
    
    /**
     * Delete from database
     *
     * @param void
     * @return boolean
     */
    function delete() {
      $delete = parent::delete();
      
      if($delete && !is_error($delete)) {
        switch ($this->getType()) {
          case COMPANY_CONFIG_OPTION:
            CompanyConfigOptions::deleteByOption($this->getName());
            break;
          case USER_CONFIG_OPTION:
            UserConfigOptions::deleteByOption($this->getName());
            break;
          case PROJECT_CONFIG_OPTION:
            ProjectConfigOptions::deleteByOption($this->getName());
            break;
        } // if
      } // if
      
      return $delete;
    } // delete
  
  }

?>