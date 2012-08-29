<?php

  /**
   * Print JS langs if locale is set
   * 
   * Parameters:
   * 
   * - locale - helper will print needed javascript language translations for this
   * locale
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_js_langs($params, &$smarty) {
    if (LOCALIZATION_ENABLED && $locale = array_var($params, 'locale', null)) {
      $cache_id = 'lang_cache_for_'.$locale;
      
      $cached_value = cache_get($cache_id);
      if (empty($cached_value)) {
        $cached_value = array();
        
        $ld_path = LOCALIZATION_PATH .'/'.$locale;
        if(is_dir($ld_path)) {
          $ld = dir($ld_path);
          $sub_langs = array();
          while(($sub_entry = $ld->read()) !== false) {
            if($sub_entry[0] == '.' || substr($sub_entry, 0, 7)!='module.') {
              continue;
            } // if
            
            $module_name = substr($sub_entry,7,-4);
            $dictionary = array();
            
            $module_lang = (array) include(LOCALIZATION_PATH .'/'.$locale.'/'.$sub_entry);
            
            $module_application_path = APPLICATION_PATH . '/modules/' . $module_name;
            if (is_dir($module_application_path)) {
              if (is_file($module_application_path . '/lang_index_js.php')) {
                $dictionary = (array) require($module_application_path . '/lang_index_js.php');
              }
              
              if ($module_name == 'system') {
                if (is_file($module_application_path . '/lang_index_js_common.php')) {
                  $dictionary = array_merge((array) require($module_application_path . '/lang_index_js_common.php'), $dictionary);
                }
              } // if
            } // if
                    
            foreach ($dictionary as $dictionary_word) {
              $cached_value[$dictionary_word] = isset($module_lang[$dictionary_word]) ? $module_lang[$dictionary_word] : '';
            } // foreach
          }
        } // if
      }
      cache_set($cache_id, $cached_value);
    } // if
    
    $result = '<script type="text/javascript">' . "\nApp.langs = {\n";
    if(isset($cached_value) && is_foreachable($cached_value)) {
      $iteration = 0;
      $count = count($cached_value);
      foreach($cached_value as $k => $v) {
        $iteration++;
        
        $result .= var_export($k, true) . ':' . var_export($v, true);
        if($iteration == $count) {
          $result .= "\n";
        } else {
          $result .= ",\n";
        }
      } // foreach
    } // if
    return $result . "};\n</script>";
  } // smarty_function_js_langs

?>