<?php

  // Build on top of administration controller
  use_controller('admin', SYSTEM_MODULE);

  /**
   * Languages administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class LanguagesAdminController extends AdminController {
    
    /**
     * Name of the controller (for PHP4 compatibility)
     *
     * @var string
     */
    var $controller_name = 'languages_admin';
    
    /**
     * Selected language
     *
     * @var Language
     */
    var $active_language;
    
    /**
     * Construction languages administration controller
     *
     * @param Request $request
     * @return LanguagesAdminController
     */
    function __construct($request) {
    	parent::__construct($request);
    	
    	if(!LOCALIZATION_ENABLED) {
    	  flash_error('Localization support is disabled. Please enabled it before you can access Language administration');
    	  $this->redirectTo('admin');
    	} // if
    	
    	$this->wireframe->addBreadCrumb(lang('Languages'), assemble_url('admin_languages'));
    	
    	$language_id = $this->request->getId('language_id');
    	if($language_id) {
    	  $this->active_language = Languages::findById($language_id);
    	  if(instance_of($this->active_language, 'Language')) {
    	    $this->wireframe->addBreadCrumb($this->active_language->getName(), $this->active_language->getViewUrl());
    	  } // if
    	} else {
    	  $this->active_language = new Language();
    	} // if
    	
    	$this->smarty->assign('active_language', $this->active_language);
    } // __construct
    
    /**
     * Show main languages page
     *
     * @param void
     * @return null
     */
    function index() {
      if(extension_loaded('xml') && function_exists('xml_parser_create')) {
        $this->wireframe->addPageAction(lang('Import Language'), assemble_url('admin_languages_import'));
      } else {
        $this->wireframe->addPageMessage(lang('XML extension needs to be loaded in your PHP installation for activeCollab to be able to read XML language files. Please check <a href="http://www.php.net/manual/en/book.xml.php">this page</a> for details'), 'error');
      } // if
      
      $this->smarty->assign(array(
        'languages' => Languages::findAll(),
        'default_language_id' => ConfigOptions::getValue('language'),
      ));
    } // index
    
    /**
     * View language details
     *
     * @param void
     * @return null
     */
    function view() {
    	if($this->active_language->isNew()) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	    	
    	$this->smarty->assign(array(
    	  'translation_files' => $this->active_language->getTranslationFiles(),
    	  'dictionaries' => $this->active_language->getAvailableDictionaries(),
    	));
    } // view
    
    /**
     * Create a new language
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
    	$language_data = $this->request->post('language');
    	$this->smarty->assign('language_data', $language_data);
    	
    	if($this->request->isSubmitted()) {
    	  $this->active_language = new Language();
    	  $this->active_language->setAttributes($language_data);
    	  
    	  $locale = array_var($language_data, 'locale');
    	  if($locale && !str_ends_with(strtolower($locale), 'utf-8')) {
    	    $locale .= '.UTF-8';
    	    $this->active_language->setLocale($locale);
    	  } // if
    	  
    	  $save = $this->active_language->save();
    	  if($save && !is_error($save)) {
    	    $localization_folder = $this->active_language->getLocalizationPath();
    	    if(!is_dir($localization_folder)) {
    	      if(@mkdir($localization_folder)) {
    	        @chown($localization_folder, 0777); // make and chown
    	      } // if
    	    } // if
    	    
    	    flash_success('Language ":name" has been added', array('name' => $this->active_language->getName()));
    	    $this->redirectTo('admin_languages');
    	  } else {
    	    $this->smarty->assign('errors', $save);
    	  } // if
    	} // if
    } // add
    
    /**
     * Update language information
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
    	if($this->active_language->isNew()) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	$language_data = $this->request->post('language');
    	if(!is_array($language_data)) {
    	  $language_data = array(
    	    'name' => $this->active_language->getName(),
    	    'locale' => $this->active_language->getLocale(),
    	  );
    	} // if
    	$this->smarty->assign('language_data', $language_data);
    	
    	if($this->request->isSubmitted()) {
    	  $old_name = $this->active_language->getName();
    	  $this->active_language->setAttributes($language_data);
    	  
    	  $save = $this->active_language->save();
    	  if($save && !is_error($save)) {
    	    flash_success('Language ":name" has been update', array('name' => $old_name));
    	    $this->redirectTo('admin_languages');
    	  } else {
    	    $this->smarty->assign('errors', $save);
    	  } // if
    	} // if
    } // edit
    
    /**
     * Remove specific language
     *
     * @param void
     * @return null
     */
    function delete() {
    	if($this->active_language->isNew()) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_language->canDelete($this->logged_user)) {
    	  $this->httpError(HTTP_ERR_FORBIDDEN);
    	} // if
    	
    	if($this->request->isSubmitted()) {
  	    if($this->active_language->delete()) {
      	  flash_success('Language ":name" has been deleted', array('name' => $this->active_language->getName()));
      	} else {
      	  flash_error('Failed to delete ":name" language', array('name' => $this->active_language->getName()));
      	} // if
      	
      	$this->redirectTo('admin_languages');
    	} else {
    	  $this->httpError(HTTP_ERR_BAD_REQUEST);
    	} // if
    } // delete
    
    /**
     * Set specific language as default
     *
     * @param void
     * @return null
     */
    function set_as_default() {
    	if($this->request->isSubmitted()) {
    	  if($this->active_language->isNew()) {
    	    $this->httpError(HTTP_ERR_NOT_FOUND);
    	  } // if
    	  
    	  ConfigOptions::setValue('language', $this->active_language->getId());
    	  
    	  flash_success(':name language has been set as default', array('name' => $this->active_language->getName()));
    	  $this->redirectTo('admin_languages');
    	} else {
    	  $this->httpError(HTTP_ERR_BAD_REQUEST);
    	} // if
    } // set_as_default
    
    /**
     * Creates translation file in chosed language
     * 
     * @param void
     * @return void
     *
     */
    function add_translation_file() {
    	if($this->active_language->isNew()) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if($this->request->isSubmitted()) {
      	$available_dictionaries = $this->active_language->getAvailableDictionaries();
      	
      	$dictionary = $this->request->post('dictionary');
      	if (!$dictionary || !is_foreachable($available_dictionaries) || !in_array($dictionary, $available_dictionaries)) {
      	  $this->httpError(HTTP_DOWNLOAD_E_INVALID_REQUEST);
      	} // if
      	
    	  $result = $this->active_language->createTranslationFile($dictionary);
    	  if ($result == true) {
    	    flash_success('Successfully created translation file from :dictionary dictionary', array('dictionary' => $dictionary));
    	    $this->redirectToUrl($this->active_language->getEditTranslationFileUrl($dictionary));
    	  } else {
    	    flash_error('Cannot create translation file');
    	    $this->redirectToUrl($this->active_language->getViewUrl());
    	  } // if
    	} else {
    	  $this->httpError(HTTP_ERR_BAD_REQUEST);
    	} // if
    } // add_translation_file
    
    /**
     * Edit translation file in chosen language
     * 
     * @param void
     * @return void
     */
    function edit_translation_file() {
    	if($this->active_language->isNew()) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	$translation_id = $this->request->get('filename');
    	if (!$translation_id) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	$dictionary_filename = Languages::getDictionaryPath($translation_id);
    	if(!is_file($dictionary_filename)) {
    	  flash_error('Dictionary does not exists');
    	  $this->redirectToUrl($this->active_language->getViewUrl());
    	} // if
    	
    	$dictionary = Languages::getDictionary($translation_id);
    	
    	$translation_file = Languages::getTranslationPath($this->active_language, $translation_id);
    	if (!is_file($translation_file)) {
    	  flash_error('Translation file does not exists. You need to create it first.');
    	  $this->redirectToUrl($this->active_language->getViewUrl());
    	} // if
    	
    	$translation_data = Languages::getTranslation($this->active_language, $translation_id);
    	
    	$prepared_form_data = $this->request->post('form_data');
    	if (!is_array($prepared_form_data)) {
      	$prepared_form_data = array();
      	foreach ($dictionary as $dictionary_id => $dictionary_value) {
      	  $prepared_form_data[$dictionary_id] = array(
      	   "dictionary_value" => $dictionary_value,
      	   "translated_value" => array_var($translation_data, $dictionary_value),
      	  );
      	} // foreach
      	      	
      	$this->smarty->assign(array(
      	 "prepared_form_data" => $prepared_form_data,
      	));
    	} // if
    	
    	$this->smarty->assign(array(
    	 "translation_file" => $translation_id,
    	 "form_url" => $this->active_language->getEditTranslationFileUrl($translation_id),
    	));
    	
    	if ($this->request->isSubmitted()) {
    	  if (is_foreachable($prepared_form_data)) {
    	    $new_prepared_data = array();
    	    $translation_data = array();
    	    foreach ($prepared_form_data as $prepared_form_data_key => $prepared_form_data_value ) {
    	    	$translation_data[array_var($dictionary, $prepared_form_data_key)] = $prepared_form_data_value;
    	    	$new_prepared_data[$prepared_form_data_key] = array(
        	   "dictionary_value" => array_var($dictionary, $prepared_form_data_key),
        	   "translated_value" => $prepared_form_data_value,
    	    	);
    	    } // foreach
    	  } // if
    	  file_put_contents($translation_file, '<?php return '.var_export($translation_data,true).' ?>');
    	  cache_remove_by_pattern('lang_cache_for_*');
    	  
    	  if (module_loaded('incoming_mail')) {
      	  // set config option for translation
      	  if (array_key_exists(EMAIL_SPLITTER, $translation_data)) {
      	    $config_option = ConfigOptions::getValue('email_splitter_translations');
      	    $config_option[$this->active_language->getLocale()] = $translation_data[EMAIL_SPLITTER];
      	    ConfigOptions::setValue('email_splitter_translations', $config_option);
      	  } // if
    	  } // if
    	  
      	$this->smarty->assign(array(
      	 "prepared_form_data" => $new_prepared_data,
      	));
    	} // if
    } // edit_translation_file
    
    /**
     * Import language from XML form
     * 
     * @param void
     * @return null
     */
    function import() {
      if(!extension_loaded('xml') || !function_exists('xml_parser_create')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $import_url = assemble_url('admin_languages_import');
      $this->wireframe->addBreadCrumb(lang('Import Language'), $import_url);
      
      $step = $this->request->post('wizard_step') ? $this->request->post('wizard_step') : 'initial';
      
      if ($step == 'initial') {
        $next_step = 'review';
      } else if ($step == 'review') {
        $next_step = 'finalize';
      } // if
      
  	  if (!folder_is_writable(LOCALIZATION_PATH)) {
  	    $this->wireframe->addPageMessage(lang('Localization folder: <strong>:folder</strong> is not writable', array('folder' => LOCALIZATION_PATH)), PAGE_MESSAGE_ERROR);
  	    $this->smarty->assign('import_enabled' , false);
  	  } else {
        $this->smarty->assign(array(
          'import_url'      => $import_url,
          'step'            => $step,
          'next_step'       => $next_step,
          'import_enabled'  => true
        ));
        
        if ($this->request->isSubmitted()) {
          switch ($step) {
          	case 'initial':
          		
          	 break;
          		
          	case 'review':
              $xml_file = $_FILES['xml']['tmp_name'];
              if (!is_file($xml_file)) {
                flash_error('You need to upload XML file first');
                $this->redirectToReferer($import_url);
              } // if
              
              require_once(ANGIE_PATH.'/classes/xml/xml2array.php');
              $language = xml2array(file_get_contents($xml_file));
              if (!$language) {
                flash_error('Language XML file is corrupted');
                $this->redirectToReferer($import_url);
              } // if
                         
              $locale = $language['language']['info']['locale']['value'];
              $name = $language['language']['info']['name']['value'];
              $ac_version = $language['language']['info']['ac_version']['value'];
              $system_version = $this->application->version ? $this->application->version : '1.0';
              
              if (!$locale || !$name) {
                flash_error('Language XML file is corrupted');
                $this->redirectToReferer($import_url);
              } // if
              
              if (Languages::localeExists($locale)) {
                flash_error('Language with locale :locale is already installed on system', array('locale' => $locale));  
                $this->redirectToReferer($import_url);
              } // if
              
              if (Languages::nameExists($name)) {
                flash_error('Language with name :name is already installed on system', array('name' => $name));
                $this->redirectToReferer($import_url);
              } // if
              
              $attachment = make_attachment($_FILES['xml']);
              if (!$attachment || is_error($attachment)) {
                flash_error($attachment->getMessage(), array('name' => $name));
                $this->redirectToReferer($import_url);             
              } // if
                          
              if (version_compare($ac_version, $system_version, '=') != true) {
                $this->wireframe->addPageMessage(lang('Current activeCollab version is <strong>:system_version</strong> and this translation is made for <strong>:ac_version</strong> version. Importing can continue, but this translation may not work on your system', array(
                  'system_version' => $system_version,
                  'ac_version' => $ac_version,
                )), 'warning');
              } // if
                         
              
              $this->smarty->assign(array(
                'language_ac_version' => $ac_version,
                'language_name'       => $name,
                'language_locale'     => $locale,
                'system_version'      => $system_version,
                'attachment_id'       => $attachment->getId(),
              ));
              
              $this->setTemplate('import_review');
          	 break;
          	 
          	case 'finalize':
          	  $attachment_id = $this->request->post('attachment_id');
          	  $attachment = Attachments::findById($attachment_id);
          	  if (!instance_of($attachment, 'Attachment')) {
          	    flash_error('There was some unknown error, please try again');
          	    $this->redirectTo($import_url);
          	  } // if
          	            	  
              require_once(ANGIE_PATH.'/classes/xml/xml2array.php');
              $language_array = xml2array(file_get_contents(UPLOAD_PATH.'/'.$attachment->getLocation()));
              if (!$language_array) {
                flash_error('Uploaded file is not valid XML');  
                $this->redirectToReferer($import_url);
              } // if
              
              $language_locale = $language_array['language']['info']['locale']['value'];
              $language_name = $language_array['language']['info']['name']['value'];
              $language_version = $language_array['language']['info']['ac_version']['value'];
              
          	  $language = new Language();
          	  $language->setLocale($language_locale);
          	  $language->setName($language_name);
          	                
          	  $result = recursive_mkdir($language->getLocalizationPath(), 0777, LOCALIZATION_PATH);         	  
          	  if (!$result) {
          	    flash_error('Could not create localization folder');
          	    $this->redirectToReferer($import_url);
          	  } // if

              $save = $language->save();
          	  if (!$save || is_error($save)) {
          	    flash_error($save->getMessage());
          	    $this->redirectToReferer($import_url);
          	  } // if

              $info = array(
                'name'    => $language_name,
                'code'    => $language_locale,
                'version' => $language_version
              );
              
              $result = file_put_contents($language->getLocalizationPath().'/info.php', "<?php return ".var_export($info, true)." ?>");

              if (is_foreachable($language_array['language']['translations']['module'])) {
                foreach ($language_array['language']['translations']['module'] as $module_translation) {
                  if (is_foreachable($module_translation['translation'])) {
                    $module_name = $module_translation['attr']['name'];
                    $output = array();
                    foreach ($module_translation['translation'] as $translation) {
                      if (is_array($translation)) {
                        $phrase = $translation['attr']['phrase'];
                        $translation_value = $translation['value'];
                        $output[$phrase] = $translation_value;
                      } // if
                    } // foreach
            	    	$filename = Languages::getTranslationPath($language, $module_name);
            	    	file_put_contents($filename, "<?php return ".var_export($output, true)." ?>");
                  } // if
                } // foreach
              } // if
              
              $attachment->delete();
              flash_success('Language imported successfully');
              $this->redirectTo('admin_languages');
          	  break;
          
          	default:
          	 break;
          }
        } // if
  	  } // if
    } // import
    
    /**
     * Exports language in XML form
     * 
     * @param void
     * @return null
     */
    function export() {
      $this->wireframe->print_button = false;
      $this->skip_layout = true;
      
    	if($this->active_language->isNew()) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	$translation_files = $this->active_language->getTranslationFiles();
    	if (is_foreachable($translation_files)) {
    	  $translations_path = $this->active_language->getLocalizationPath();
        foreach ($translation_files as $translation_file) {
          $translation_filename = $translations_path.'/module.'.$translation_file.'.php';
          if (is_file($translation_filename)) {
            $translation_content = require($translation_filename);
            if (is_foreachable($translation_content)) {
              foreach ($translation_content as $phrase => $translation) {
                if ($translation) {
              	 $translations[$translation_file][$phrase] = $translation;
                } // if
              } // forach
            } // if
          } // if
        } // foreach
    	} // if
    	
    	$this->smarty->assign(array(
    	 'ac_version'      =>  $this->application->version ? $this->application->version : '1.0',
    	 'translations'    =>  $translations,
    	));
    	
    	$xml = $this->smarty->fetch($this->getTemplatePath());
      header('Content-Type: application/xml; charset=utf-8');
      download_contents($xml, 'application/xml', clean($this->active_language->getName()).' ('.$this->active_language->getLocale().').xml', true, true);
    } // export
    
  }

?>