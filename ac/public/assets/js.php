<?php

  define('ASSETS_PATH', dirname(__FILE__));
  define('CONFIG_FILE_PATH', realpath(ASSETS_PATH . '/../../config/config.php'));
  
  if(is_file(CONFIG_FILE_PATH)) {
    require_once CONFIG_FILE_PATH;
  } // if
  
  if(!defined('COMPRESS_ASSET_REQUESTS')) {
    define('COMPRESS_ASSET_REQUESTS', true);
  } // if

  if(COMPRESS_ASSET_REQUESTS && extension_loaded('zlib') && !((boolean) ini_get('zlib.output_compression'))) {
    @ob_start('ob_gzhandler');
  } else {
    ob_start();
  } // if
  
  // Turn magic quotes OFF
  if(get_magic_quotes_gpc()) {
    set_magic_quotes_runtime(0);
  } // if
  
  header("Content-type: text/javascript; charset: UTF-8");
  header("Cache-Control: max-age=315360000");
  header("Expires: " . gmdate("D, d M Y H:i:s", time() + 3600) . " GMT");
  
  $files = array(
    ASSETS_PATH . '/javascript/date.js',
    ASSETS_PATH . '/javascript/jquery.js',
    ASSETS_PATH . '/javascript/jquery.dimensions.js',
    ASSETS_PATH . '/javascript/jquery.bgiframe.js',
    ASSETS_PATH . '/javascript/jquery.form.js',
    ASSETS_PATH . '/javascript/jquery.blockui.js',
    ASSETS_PATH . '/javascript/jquery.jeditable.js',
    ASSETS_PATH . '/javascript/jquery.uni-form.js',
    ASSETS_PATH . '/javascript/jquery.checkboxes.js',
    ASSETS_PATH . '/javascript/jquery.datepicker.js',
    ASSETS_PATH . '/javascript/jquery.scrollTo.js',
    ASSETS_PATH . '/javascript/jquery.scrollTo.js',
    ASSETS_PATH . '/javascript/jquery.scalebigimages.js',
    ASSETS_PATH . '/javascript/jquery.ui.js',
    ASSETS_PATH . '/javascript/jquery.cookie.js',
    ASSETS_PATH . '/javascript/jquery.insertAtCursor.js',
//  BEGIN: TREE COMPONENT
    ASSETS_PATH . '/javascript/jquery.tree_component.js',
    ASSETS_PATH . '/javascript/jquery.tree_component_css.js',
    ASSETS_PATH . '/javascript/jquery.listen.js',
//  END: TREE COMPONENT
    ASSETS_PATH . '/javascript/app.js',
    ASSETS_PATH . '/modules/system/javascript/main.js',
    ASSETS_PATH . '/modules/resources/javascript/main.js',
  );
  
  $modules = isset($_GET['modules']) && $_GET['modules'] ? explode(',', trim($_GET['modules'], ',')) : null;
  
  $d = dir(ASSETS_PATH . '/modules');
  while(($entry = $d->read()) !== false) {
    if($entry == '.' || $entry == '..' || $entry == 'system' || $entry == 'resources') {
      continue;
    } // if
    
    if($modules && !in_array($entry, $modules)) {
      continue;
    } // if
    
    $files[] = ASSETS_PATH . '/modules/' . $entry . '/javascript/main.js';
  } // while
  $d->close();
  
  foreach($files as $file) {
    if(!is_file($file)) {
      continue;
    } // if
    
    print "\n\n/** File: " . substr($file, strlen(ASSETS_PATH) + 1) . " **/\n\n";
    
    $f = fopen($file, 'r');
    $filesize = filesize($file);
    if($filesize) {
      print fread($f, $filesize);
    } // if
    fclose($f);
  } // foreach

?>