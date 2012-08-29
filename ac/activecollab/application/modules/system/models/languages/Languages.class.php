<?php

  /**
   * Languages class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Languages extends BaseLanguages {
  
    /**
     * Return all languages ordered by name
     *
     * @param void
     * @return array
     */
    function findAll() {
      return Languages::find(array(
        'order' => 'name',
      ));
    } // findAll
    
    /**
     * Return language by locale
     *
     * @param string $locale
     * @return Language
     */
    function findByLocale($locale) {
      return Languages::find(array(
        'conditions' => array('locale = ?', $locale),
        'one' => true
      ));
    } // findByLocale
    
    /**
     * Returns path to dictionary file
     *
     * @param string $dictionary
     * @return string
     */
    function getDictionaryPath($dictionary) {
      return APPLICATION_PATH.'/modules/'.$dictionary.'/dictionary.php';
    } // getDictionaryPath
    
    /**
     * Returns dictionary $name. If there is no such dictionary returns false
     *
     * @param string $name
     * @return array
     */
    function getDictionary($name) {
      $dictionary_path = Languages::getDictionaryPath($name);
      if (!is_file($dictionary_path)) {
        return false;
      } // if
      return require($dictionary_path);
    } // getDictionary
    
    /**
     * Returns path to translation file with name $translation for $language
     *
     * @param Language $language
     * @param string $translation
     * @return string
     */
    function getTranslationPath($language, $translation) {
      if (!instance_of($language, 'Language')) {
        return false;
      } // if
      return $language->getLocalizationPath().'/module.'.$translation.'.php';
    } // getTranslationPath
    
    
    /**
     * Returns trnslation for $language and $translation parameter
     *
     * @param Language $language
     * @param string $translation
     * @return array
     */
    function getTranslation($language, $translation) {
      $translation_file = Languages::getTranslationPath($language, $translation);
      if(!is_file($translation_file)) {
        return false;
      } // if

      return require($translation_file);
    } // getTranslation
    
    /**
     * Check if $locale is already defined in system
     *
     * @param string $locale
     * @return boolean
     */
    function localeExists($locale) {
      return (boolean) Languages::count(array('locale = ?', $locale));
    } // localeExists
    
    /**
     * Check if $name is already used in system
     *
     * @param string $name
     * @return boolean
     */
    function nameExists($name) {
      return (boolean) Languages::count(array('name = ?', $name));
    } // nameExists
    
  }

?>