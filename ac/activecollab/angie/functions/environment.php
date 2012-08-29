<?php

  // ------------------------------------------------------------
  //  Error related functions
  // ------------------------------------------------------------

  /**
   * Check if specific variable is error object
   *
   * @param mixed $var Variable that need to be checked
   * @return boolean
   */
  function is_error($var) {
    return instance_of($var, 'Error');
  } // is_error

  /**
   * Find and include specific error class
   *
   * @param string $error_class
   * @return FileDnxError if file does not exists, true otherwise
   */
  function use_error($error_class) {
    if(class_exists($error_class)) {
      return true;
    } // if
    
    $expected_path = ANGIE_PATH . "/classes/errors/$error_class.class.php";
    
    if(is_file($expected_path)) {
      include_once $expected_path;
      return true;
    } else {
      return new FileDnxError($expected_path);
    } // if
  } // use_error
  
  /**
   * Show nice error output.
   *
   * @param Error $error
   * @param boolean $die Die when done, default value is true
   * @return null
   */
  function dump_error($error, $die = true) {
    static $css_rendered = false;
    
    if(!instance_of($error, 'Error')) {
      print '$error is not valid <i>Error</i> instance!';
      return;
    } // if
    
    include ANGIE_PATH . '/templates/dump_error.php';
    
    if($die) {
      die();
    } // if
  } // dump_error
  
  // ------------------------------------------------------------
  //  Environment functions
  // ------------------------------------------------------------
  
  /**
   * Contruct controller and execute specific action
   *
   * @param Request $request
   * @return null
   * @throws ControllerDnxError
   * @throws InvalidControllerActionError
   */
  function execute_action($request) {
    $controller_name = $request->getController(); // we'll use this a lot
    
    $use_controller = use_controller($controller_name, $request->getModule());
    if(is_error($use_controller)) {
      return $use_controller;
    } // if
    
    $controller_class = get_controller_class($controller_name);
    if(!class_exists($controller_class)) {
      use_error('ControllerDnxError');
      return new ControllerDnxError($controller_name);
    } // if
    
    $controller = new $controller_class($request);
    if(!instance_of($controller, 'Controller')) {
      use_error('ControllerDnxError');
      return new ControllerDnxError($controller_name);
    } // if
    
    return $controller->execute($request->getAction());
  } // execute_action
  
  // ------------------------------------------------------------
  //  Map app stuff with files / resolve paths
  // ------------------------------------------------------------
  
  /**
   * Use model classes based on plural model name (categories, stories, 
   * news_entries etc)
   *
   * @param string $plural
   * @param string $module
   * @return null
   */
  function use_model($plural, $module = DEFAULT_MODULE) {
    if(is_array($plural)) {
      foreach($plural as $model_name) {
        use_model($model_name, $module);
      } // foreach
    } else {
      $object_class = Inflector::camelize(Inflector::singularize($plural));
      $manager_class = Inflector::camelize($plural);
      
      if(CAN_AUTOLOAD) {
        set_for_autoload(array(
          'Base' . $object_class => APPLICATION_PATH . "/modules/$module/models/$plural/Base" . $object_class . '.class.php',
          $object_class => APPLICATION_PATH . "/modules/$module/models/$plural/" . $object_class . '.class.php',
          'Base' . $manager_class => APPLICATION_PATH . "/modules/$module/models/$plural/Base" . $manager_class . '.class.php',
          $manager_class => APPLICATION_PATH . "/modules/$module/models/$plural/" . $manager_class . '.class.php'
        ));
      } else {
        require_once APPLICATION_PATH . "/modules/$module/models/$plural/Base" . $object_class . '.class.php';
        require_once APPLICATION_PATH . "/modules/$module/models/$plural/" . $object_class . '.class.php';
        require_once APPLICATION_PATH . "/modules/$module/models/$plural/Base" . $manager_class . '.class.php';
        require_once APPLICATION_PATH . "/modules/$module/models/$plural/" . $manager_class . '.class.php';
      } // if
    } // if
  } // use_model
  
  /**
   * Find and include specific controller based on controller name
   *
   * @param string $controller_name
   * @param string $module_name
   * @return boolean
   * @throws FileDnxError if controller file does not exists
   */
  function use_controller($controller_name, $module_name = DEFAULT_MODULE) {
    $controller_class = get_controller_class($controller_name);
    if(class_exists($controller_class)) {
      return true;
    } // if
    
    $controller_file = APPLICATION_PATH . "/modules/$module_name/controllers/$controller_class.class.php";

    if(is_file($controller_file)) {
      include_once $controller_file;
      return true;
    } else {
      return new FileDnxError($controller_file, "Controller $module_name::$controller_name does not exists (expected location '$controller_file')");
    } // if    
  } // use_controller
  
  /**
   * Return controller name based on controller class
   *
   * @param string $controller_class
   * @return string
   */
  function get_controller_name($controller_class) {
    return Inflector::underscore(substr($controller_class, 0, strlen($controller_class) - 10));
  } // get_controller_name
  
  /**
   * Return controller class based on controller name
   *
   * @param string $controller_name
   * @return string
   */
  function get_controller_class($controller_name) {
    return Inflector::camelize($controller_name) . 'Controller';
  } // get_controller_name
  
  /**
   * Return path of specific template
   *
   * @param string $template
   * @param string $controller_name
   * @param string $module_name
   * @return string
   */
  function get_template_path($template, $controller_name = null, $module_name = DEFAULT_MODULE) {
    if($controller_name) {
      return APPLICATION_PATH . "/modules/$module_name/views/$controller_name/$template.tpl";
    } else {
      return APPLICATION_PATH . "/modules/$module_name/views/$template.tpl";
    } // if
  } // get_template_path
  
  /**
   * Return layout
   *
   * @param string $layout
   * @param string $module
   * @return string
   */
  function get_layout_path($layout, $module = DEFAULT_MODULE) {
    return APPLICATION_PATH . "/modules/$module/layouts/$layout.tpl";
  } // get_layout_path
  
  // ---------------------------------------------------
  //  Assets
  // ---------------------------------------------------
  
  /**
   * Return asset URL
   *
   * @param string $name
   * @return string
   */
  function get_asset_url($name) {
    return ASSETS_URL . '/' . $name;
  } // get_asset_url
  
  /**
   * Return stylesheet URL
   *
   * .css extension is automatically appended
   * 
   * @param string $name
   * @param string $module
   * @return string
   */
  function get_stylesheet_url($name, $module = null) {
    $filename = $name ? $name . '.css' : '';
    
    $prefix = $module ? ASSETS_URL . "/modules/$module" : ASSETS_URL;
    return $prefix . "/stylesheets/$filename";
  } // get_stylesheet_url
  
  /**
   * Return javascript URL
   * 
   * .js extension is automatically appended
   *
   * @param string $name
   * @param string $module
   * @return string
   */
  function get_javascript_url($name, $module = null) {
    $filename = $name ? $name . '.js' : '';
    
    $prefix = $module ? ASSETS_URL . "/modules/$module" : ASSETS_URL;
    return $prefix . "/javascript/$filename";
  } // get_javascript_url
  
  /**
   * Return image URL
   * 
   * $name needs to have proper extension (no extension is automatically 
   * appended)
   *
   * @param string $name
   * @param string $module
   * @return string
   */
  function get_image_url($name, $module = null) {
    $prefix = $module ? ASSETS_URL . "/modules/$module" : ASSETS_URL;
    return $prefix . "/images/$name";
  } // get_image_url
  
  /**
   * Return image path
   * 
   * $name needs to have proper extension (no extension is automatically 
   * appended)
   *
   * @param string $name
   * @param string $module
   * @return string
   * */
  function get_image_path($name, $module = null) {
    $prefix = PUBLIC_PATH.'/assets';
    if (!$module) {
      $prefix.='/images/';
    } else {
      $prefix.='/modules/'.$module.'/images/';
    } // if
    
    return $prefix.$name;
  } // get_image_path
  
  // ---------------------------------------------------
  //  Assign vars to JS
  // ---------------------------------------------------
  
  /**
   * Make $var available in JS through App.data
   *
   * @param string $var
   * @param mixed $value
   * @return null
   */
  function js_assign($var, $value = null) {
    if(is_array($var)) {
      foreach($var as $k => $v) {
      	gs_array_set_field('assigned_to_js', $k, $v);
      } // foreach
    } else {
      gs_array_set_field('assigned_to_js', $var, $value);
    } // if
  } // js_assign
  
  // ---------------------------------------------------
  //  Resources
  // ---------------------------------------------------
  
  /**
   * Return $content in selected language and insert $params in it
   *
   * @param string $content
   * @param array $params
   * @param boolean $clean_params
   * @param Language $language
   * @return string
   */
  function lang($content, $params = null, $clean_params = true, $language = null) {
    if(LOCALIZATION_ENABLED) {
      $locale = $language && instance_of($language, 'Language') ? $language->getLocale() : $GLOBALS['current_locale'];
      if($locale == BUILT_IN_LOCALE) {
        $result = $content;
      } else {
        if(!isset($GLOBALS['current_locale_translations'][$locale])) {
          if(!instance_of($language, 'Language')) {
            $language = Languages::findByLocale($locale);
          } // if
          
          if(instance_of($language, 'Language')) {
            $GLOBALS['current_locale_translations'][$locale] = array();
            $language->loadTranslations($locale);
          } else {
            $GLOBALS['current_locale_translations'][$locale] = null;
          } // if
        } // if
        
        $result = isset($GLOBALS['current_locale_translations'][$locale]) && isset($GLOBALS['current_locale_translations'][$locale][$content]) && $GLOBALS['current_locale_translations'][$locale][$content] ? 
          array_var($GLOBALS['current_locale_translations'][$locale], $content, $content) : 
          $content;
      } // if
    } else {
      $result = $content;
    } // if
    
    if(is_foreachable($params)) {
      foreach($params as $k => $v) {
        $set = $clean_params ? clean($v) : $v;
        $result = str_replace(":$k", $set, $result);
      } // foreach
    } // if
    return $result;
  } // lang
  
  // ---------------------------------------------------
  //  Converters
  // ---------------------------------------------------
  
  /**
   * Cast row data to date value (object of DateValue class)
   *
   * @param mixed $value
   * @return DateValue
   */
  function dateval($value) {
    if(empty($value)) {
      return null;
    } // if
    
    if(instance_of($value, 'DateValue')) {
      return $value;
    } elseif(is_int($value) || is_string($value)) {
      return new DateValue($value);
    } else {
      return null;
    } // if
  } // dateval
  
  /**
   * Cast raw datetime format (string) to DateTimeValue object
   *
   * @param string $value
   * @return DateTimeValue
   */
  function datetimeval($value) {
    if(empty($value)) {
      return null;
    } // if
    
    if(instance_of($value, 'DateTimeValue')) {
      return $value;
    } elseif(instance_of($value, 'DateValue')) {
      return new DateTimeValue($value->toMySQL());
    } elseif(is_int($value) || is_string($value)) {
      return new DateTimeValue($value);
    } else {
      return null;
    } // if
  } // datetimeval
  
  /**
   * Cast raw value to boolean value
   *
   * @param mixed $value
   * @return boolean
   */
  function boolval($value) {
    return (boolean) $value;
  } // boolval
  
  /**
   * Finds first available filename in uploads folder
   * 
   * @param void
   * @return string full path to file
   */
  function get_available_uploads_filename() {
    do {
      $filename = UPLOAD_PATH . '/' . make_string(10) . '-' . make_string(10) . '-' . make_string(10) . '-' . make_string(10);
    } while(is_file($filename));
    return $filename;
  } // get_available_uploads_filename

?>