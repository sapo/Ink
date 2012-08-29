<?php
  define('MODULE_ASSETS_PATH', dirname(__FILE__));
  define('ASSETS_PATH', MODULE_ASSETS_PATH.'/../..');
  
  $device = isset($_GET['device']) ? $_GET['device'] : null;

  if(extension_loaded('zlib') && !((boolean) ini_get('zlib.output_compression'))) {
    @ob_start('ob_gzhandler');
  } else {
    ob_start();
  } // if
  
  header("Content-type: text/javascript; charset: UTF-8");
  header("Cache-Control: must-revalidate");
  header("Expires: " . gmdate("D, d M Y H:i:s", time() + 3600) . " GMT");
   
  if (in_array($device, array('iphone'))) {
    $files = array(
      ASSETS_PATH . '/javascript/jquery.js',
    );
  }
  
  $device_dir = MODULE_ASSETS_PATH.'/'.$device.'/javascript';
  if ($device && is_dir($device_dir)) {
    $d = dir($device_dir);
    while(($entry = $d->read()) !== false) {
      $pathinfo = pathinfo($entry);
      if($entry == '.' || $entry == '..' || $entry == 'system' || $entry == 'resources' || $pathinfo['extension'] != 'js') {
        continue;
      } // if
      $files[] = $device_dir.'/'.$entry;
    } // while
    $d->close();    
  }
  
  foreach($files as $file) {
    if(!is_file($file)) {
      continue;
    } // if
    
    print "\n\n/** File: " . substr($file, strlen(ASSETS_PATH) + 1) . " **/\n\n";
    
    if(function_exists('file_get_contents')) {
      print file_get_contents($file);
    } else {
      $f = fopen($file, 'r');
      $filesize = filesize($file);
      if($filesize) {
        print fread($f, $filesize);
      } // if
      fclose($f);
    } // if
  } // foreach

?>