<?php

  /**
   * Language class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Language extends BaseLanguage {
    
    /**
     * Cached translation files array
     *
     * @var array
     */
    var $translation_files = false;
    
    /**
     * Load translations into given array
     *
     * @param string $locale
     * @return null
     */
    function loadTranslations($locale) {
      $files = $this->getTranslationFiles();
            
      if(is_foreachable($files)) {
        foreach ($files as $file) {
          $GLOBALS['current_locale_translations'][$locale] = array_merge($GLOBALS['current_locale_translations'][$locale], (array) Languages::getTranslation($this, $file));
        } // foreach
      } // if
    } // loadTranslations
    
    /**
     * Return array of translation files
     *
     * @param void
     * @return array
     */
    function getTranslationFiles() {
      if($this->translation_files === false) {
        $this->translation_files = array();
        
        $localization_path = $this->getLocalizationPath();
        $localization_path_len = strlen($localization_path);
        
        $files = get_files($localization_path, 'php');
        if(is_foreachable($files)) {
          foreach($files as $path) {
            $file = substr($path, $localization_path_len + 1);
            
            if(!str_starts_with($file, 'module.')) {
              continue;
            } // if
            
            $this->translation_files[] = substr($file, 7, strlen($file) - 11);
          } // foreach
        } // if
        
        if(count($this->translation_files) < 1) {
          $this->translation_files = null;
        } // if
      } // if
      return $this->translation_files;
    } // getTranslationFiles
    
    /**
     * Creates dictionary file in localization path
     *
     * @param string $dictionary_name
     */
    function createTranslationFile($dictionary_name) {
      $dictionary_file =Languages::getDictionaryPath($dictionary_name);
      if (!is_file($dictionary_file)) {
        return false;
      } // if
      
      $translation_file = Languages::getTranslationPath($this, $dictionary_name);
      if (!folder_is_writable(dirname($translation_file))) {
        return false;
      } // if
      
      if (is_file($translation_file)) {
        return true;
      } // if
      
      $dictionary = Languages::getDictionary($dictionary_name);
      $translation = array();
      if (is_foreachable($dictionary)) {
        foreach ($dictionary as $dictionary_word) {
        	$translation[$dictionary_word] = '';
        } // foreach
      } // if
      
      $result = file_put_contents($translation_file, "<?php return ".var_export($translation, true)." ?>");
      if (!$result) {
        return false;
      } // if
      
      return true;
    } // createTranslationFile
    
    /**
     * Array of available dictionary files
     *
     * @var array
     */
    var $available_dictionaries = false;
    
    /**
     * Return all available dictionary files
     *
     * @param void
     * @return array
     */
    function getAvailableDictionaries() {
      if($this->available_dictionaries === false) {
        $dictionaries = array();
        
        $translations = $this->getTranslationFiles();
        if(!is_array($translations)) {
          $translations = array();
        } // if
        
      	$modules = Modules::findAll();
      	
      	if(is_foreachable($modules)) {
      	  foreach($modules as $module) {
      	    if(!in_array($module->getName(), $translations) && is_file($module->getPath() . '/dictionary.php')) {
      	      $dictionaries[] = $module->getName();
      	    } // if
      	  } // foreach
      	} // if
      	
      	$this->available_dictionaries = count($dictionaries) ? $dictionaries : null;
      } // if
      
      return $this->available_dictionaries;
    } // getAvailableDictionaries
    
    /**
     * Check if specific translation file is editable
     *
     * @param string $translation_file
     * @return boolean
     */
    function isEditable($translation_file) {
    	return is_writable($this->getLocalizationPath() . "/module.$translation_file.php");
    } // isEditable
    
    /**
     * Return path to the localization directory
     *
     * @param void
     * @return string
     */
    function getLocalizationPath() {
    	return LOCALIZATION_PATH . '/' . $this->getLocale();
    } // getLocalizationPath
    
    /**
     * Returns true if this language is default
     *
     * @param void
     * @return boolean
     */
    function isDefault() {
      return $this->getId() == ConfigOptions::getValue('language');
    } // isDefault
    
    /**
     * Returns true if this locale is built in the code
     *
     * @param void
     * @return boolean
     */
    function isBuiltIn() {
    	return $this->getLocale() == BUILT_IN_LOCALE;
    } // isBuiltIn
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can delete this language
     *
     * @param User $user
     * @return boolean
     */
    function canDelete($user) {
      if($this->isBuiltIn() || $this->isDefault()) {
        return false;
      } // if
      
      return $user->isAdministrator();
    } // canDelete
  
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view language URL
     *
     * @param void
     * @return string
     */
    function getViewUrl() {
    	return assemble_url('admin_language', array(
    	  'language_id' => $this->getId(),
    	));
    } // getViewUrl
    
    /**
     * Return export language URL
     *
     * @param void
     * @return string
     */
    function getExportUrl() {
    	return assemble_url('admin_language_export', array(
    	  'language_id' => $this->getId(),
    	));
    } // getViewUrl
    
    /**
     * Return edit language URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
    	return assemble_url('admin_language_edit', array(
    	  'language_id' => $this->getId(),
    	));
    } // getEditUrl
    
    /**
     * Return view language URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
    	return assemble_url('admin_language_delete', array('language_id' => $this->getId(),));
    } // getDeleteUrl
    
    /**
     * Return set as default URL
     *
     * @param void
     * @return string
     */
    function getSetAsDefaultUrl() {
    	return assemble_url('admin_language_set_default', array('language_id' => $this->getId()));
    } // getSetAsDefaultUrl
    
    /**
     * Return add translation file URL
     *
     * @param string $module
     * @return string
     */
    function getAddTranslationFileUrl($module = null) {
      $params = array('language_id' => $this->getId());
      if($module) {
        $params['module'] = $module;
      } // if
      
    	return assemble_url('admin_language_add_translation_file', $params);
    } // getAddTranslationFileUrl
    
    /**
     * Return edit translation file URL
     *
     * @param string $filename
     * @return string
     */
    function getEditTranslationFileUrl($filename) {
    	return assemble_url('admin_language_edit_translation_file', array(
    	  'language_id' => $this->getId(),
    	  'filename' => $filename,
    	));
    } // getEditTranslationFileUrl
    
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
    	if(!$this->validateUniquenessOf('name')) {
    	  $errors->addError(lang('Language name needs to be unique'), 'name');
    	} // if
    } // validate
    
    /**
     * Removes object and files from filesystem
     * 
     * @param void
     * @return null
     */
    function delete() {
      $delete = parent::delete();
      if($delete && !is_error($delete)) {
        recursive_rmdir($this->getLocalizationPath(), LOCALIZATION_PATH);
        
        $user_config_options_table = TABLE_PREFIX . 'user_config_options';
        
        $rows = db_execute_all("SELECT user_id, value FROM $user_config_options_table WHERE name = ?", 'language');
        if(is_foreachable($rows)) {
          $used_by_users = array();
          foreach($rows as $row) {
            if(unserialize($row['value']) == $this->getId()) {
              $used_by_users[] = (integer) $row['user_id'];
            } // if
          } // foreach
          
          if(is_foreachable($used_by_users)) {
            db_execute("DELETE FROM $user_config_options_table WHERE name = ? AND user_id IN (?)", 'language', $used_by_users);
            cache_remove_by_pattern('user_config_options_*');
          } // if
        } // if
      } // if
      
      return $delete;
    } // deleted
  
  }

?>