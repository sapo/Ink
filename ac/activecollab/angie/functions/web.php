<?php

  /**
   * All web related functions - content forwarding, redirections, header 
   * manipulation etc
   *
   * @package angie.functions
   */
  
  /**
  * Redirect to specific URL (header redirection). 
  * 
  * Usually URLs passed to this function are escaped so they can be printed in templates and 
  * not break the validator (&amp; problem) so this functions undo htmlspecialchars() first
  *
  * @param string $to Redirect to this URL
  * @param boolean $die Die when finished
  * @return void
  */
  function redirect_to($to, $die = true) {
  	$to = undo_htmlspecialchars($to);
    header('Location: ' . $to);
    if($die) {
      die();
    } // if
  } // redirect_to
  
  /**
  * Redirect to referer
  *
  * @access public
  * @param string $alternative Alternative URL is used if referer is not valid URL
  * @return null
  */
  function redirect_to_referer($alternative = null) {
    $referer = get_referer();
    if($referer) {
      redirect_to($referer);
    } else {
      redirect_to($alternative);
    } // if
  } // redirect_to_referer
  
  /**
  * Return referer URL
  *
  * @param string $default This value is returned if referer is not found or is empty
  * @return string
  */
  function get_referer($default = null) {
    return array_var($_SERVER, 'HTTP_REFERER', $default);
  } // get_referer

  /**
  * Forward specific file to the browser as a stream of data. Download can be forced 
  * (dispolition: attachment) or passed inline
  *
  * @param string $path File path
  * @param string $type Serve file as this type
  * @param string $name If set use this name, else use filename (basename($path))
  * @param boolean $force_download Force download (add Disposition => attachement)
  * @param boolean $die
  * @return boolean
  */
  function download_file($path, $type = 'application/octet-stream', $name = null, $force_download = false, $die = true) {
    if(!defined('HTTP_LIB_PATH')) {
      require ANGIE_PATH . '/classes/http/init.php';
    } // if
    
    // Prepare variables
    if(empty($name)) {
      $name = basename($path);
    } // if
    
    $disposition = $force_download ? HTTP_DOWNLOAD_ATTACHMENT : HTTP_DOWNLOAD_INLINE;
    
    // Prepare and send file
    $download = new HTTP_Download();
    $download->setFile($path, true);
    $download->setContentType($type);
    $download->setContentDisposition($disposition, $name);
    
    $download->send();
    
    if($die) {
      die();
    } // if
  } // download_file
  
  /**
  * Use content (from file, from database, other source...) and pass it to the browser as a file
  *
  * @param string $content
  * @param string $type MIME type
  * @param string $name File name
  * @param integer $size File size
  * @param boolean $force_download Send Content-Disposition: attachment to force 
  *   save dialog
  * @param boolean $die
  * @return boolean
  */
  function download_contents($content, $type, $name, $force_download = false, $die = true) {
    if(!defined('HTTP_LIB_PATH')) {
      require ANGIE_PATH . '/classes/http/init.php';
    } // if
    
    // Prepare variables
    if(empty($name)) {
      $name = basename($path);
    } // if
    
    $disposition = $force_download ? HTTP_DOWNLOAD_ATTACHMENT : HTTP_DOWNLOAD_INLINE;
    
    // Prepare and send file
    $download = new HTTP_Download();
    $download->setData($content);
    $download->setContentType($type);
    $download->setContentDisposition($disposition, $name);
    
    $download->send();
    
    if($die) {
      die();
    } // if
  } // download_contents
  
  /**
   * Prepare path info
   *
   * @param void
   * @return null
   */
  function prepare_path_info() {
    if(defined('ANGIE_PATH_INFO') && defined('ANGIE_QUERY_STRING')) {
      return;
    } // if
    
    if(defined('FORCE_QUERY_STRING') && FORCE_QUERY_STRING) {
      $path_info = array_var($_GET, 'path_info');
      
      // We are using query string to pass path info here. We need to get 
      // original query string from REQUEST_URI
//      $query_string = '';
//      $request_uri = array_var($_SERVER, 'REQUEST_URI');
//      if(($pos = strpos($request_uri, '?')) !== false) {
//        $query_string = substr($request_uri, $pos + 1);
//      } // if
      if(PATH_INFO_THROUGH_QUERY_STRING && isset($_SERVER['QUERY_STRING'])) {
        $query_string = $_SERVER['QUERY_STRING'];
      } else {
        $query_string = '';
        $request_uri = array_var($_SERVER, 'REQUEST_URI');
        if(($pos = strpos($request_uri, '?')) !== false) {
          $query_string = substr($request_uri, $pos + 1);
        } // if
      } // if
    } else {
      $path_info = '';
      if(isset($_SERVER['PATH_INFO'])) {
        $path_info = $_SERVER['PATH_INFO'];
      } // if
  
      if(empty($path_info) && isset($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO']) {
        $path_info = $_SERVER['ORIG_PATH_INFO'];
      } // if
  
      if(($pos = strpos($path_info, 'index.php')) !== false) {
        $path_info = substr($path_info, $pos + 10);
      } // if
      
      $query_string = array_var($_SERVER, 'QUERY_STRING');
    } // if
    
    define('ANGIE_PATH_INFO', $path_info);
    define('ANGIE_QUERY_STRING', $query_string);
  } // prepare_path_info
  
  /**
   * This function will strip slashes if magic quotes is enabled so 
   * all input data ($_GET, $_POST, $_COOKIE) is free of slashes
   *
   * @param void
   * @return null
   */
  function fix_input_quotes() {
    if(get_magic_quotes_gpc()) {
      array_stripslashes($_GET);
      array_stripslashes($_POST);
      array_stripslashes($_COOKIE);
    } // if
  } // fix_input_quotes
  
  /**
  * This function will walk recursivly thorugh array and strip slashes from scalar values
  *
  * @param array $array
  * @return null
  */
  function array_stripslashes(&$array) {
    if(!is_array($array)) return;
    foreach($array as $k => $v) {
      if(is_array($array[$k])) {
        array_stripslashes($array[$k]);
      } else {
        $array[$k] = stripslashes($array[$k]);
      } // if
    } // foreach
    return $array;
  } // array_stripslashes
  
  /**
  * Check and set a valid protocol for a given URL
  * 
  * This function will check if $url has a protocol part and if it does not have 
  * it will add it. If $ignore_empty is set to true and $url is an emapty string 
  * empty string will be returned back (good for optional URL fields).
  *
  * @param string $url
  * @param boolean $ignore_empty
  * @param string $protocol
  * @return string
  */
  function valid_url_protocol($url, $ignore_empty = false, $protocol = 'http') {
    $trimmed = trim($url);
    if(($trimmed == '') && $ignore_empty) {
      return '';
    } // if
    
    if(strpos($trimmed, '://') === false) {
      return "$protocol://$trimmed";
    } else {
      return $trimmed;
    } // if
  } // valid_url_protocol
  
  
  /**
   * Replace spaces in URLs with %20
   *
   * @param string $url
   * @return string
   */
  function replace_url_spaces($url) {
    return str_replace(' ', '%20', $url);
  } // replace_url_spaces
  
  
  /**
   * Known user agents
   *
   */
  define('USER_AGENT_IPHONE', 'iphone');
  define('USER_AGENT_IPOD_TOUCH', 'ipodtouch');
  define('USER_AGENT_SAFARI', 'safari');
  define('USER_AGENT_FIREFOX', 'firefox');
  define('USER_AGENT_CAMINO', 'camino');
  define('USER_AGENT_OPERA', 'opera');
  define('USER_AGENT_IE', 'ie');
  define('USER_AGENT_NETSCAPE', 'netscape');
  define('USER_AGENT_KONQUEROR', 'konqueror');
  define('USER_AGENT_SYMBIAN', 'symbian');
  define('USER_AGENT_OPERA_MINI', 'opera_mini');
  define('USER_AGENT_OPERA_MOBILE', 'opera_mobile');
  define('USER_AGENT_ANDROID', 'android');
  define('USER_AGENT_BLACKBERRY','blackberry');
  define('USER_AGENT_MOBILE_IE', 'mobile_ie');
  
  define('USER_AGENT_DEFAULT', 'default'); 
  define('USER_AGENT_DEFAULT_MOBILE', USER_AGENT_IPHONE);
  
  /**
   * Determines user agent
   * 
   * This function will detemine user agent, and store it in USER_AGENT
   * 
   * @return void
   */
  function get_user_agent() {
    $user_agent = array_var($_SERVER, 'HTTP_USER_AGENT');
    
    $known_user_agents = array(
      array("pattern" => "/MSIE(.*)IEMobile/", "device_name" => USER_AGENT_MOBILE_IE),
      array("pattern" => "/BlackBerry/", "device_name" => USER_AGENT_BLACKBERRY),
      array("pattern" => "/Linux(.*)Android(.*)AppleWebKit(.*)KHTML(.*)Mobile/", "device_name" => USER_AGENT_ANDROID),
      array("pattern" => "/iPhone(.*)AppleWebKit(.*)KHTML(.*)Mobile/", "device_name" => USER_AGENT_IPHONE),  
      array("pattern" => "/iPod(.*)AppleWebKit(.*)KHTML(.*)Mobile/", "device_name" => USER_AGENT_IPOD_TOUCH),
      array("pattern" => "/SymbianOS(.*)AppleWebKit(.*)KHTML(.*)Safari/", "device_name" => USER_AGENT_SYMBIAN),
      array("pattern" => "/AppleWebKit(.*)KHTML(.*)Safari/", "device_name" => USER_AGENT_SAFARI),
      array("pattern" => "/Gecko(.*)Firefox/", "device_name" => USER_AGENT_FIREFOX),
      array("pattern" => "/Gecko(.*)Camino/", "device_name" => USER_AGENT_CAMINO),
      array("pattern" => "/Gecko(.*)Netscape/", "device_name" => USER_AGENT_NETSCAPE),
      array("pattern" => "/Opera(.*)Opera Mini/", "device_name" => USER_AGENT_OPERA_MINI),
      array("pattern" => "/MSIE(.*)Windows NT(.*)SV1(.*)Opera/", "device_name" => USER_AGENT_OPERA_MOBILE),
      array("pattern" => "/MSIE(.*)Windows CE(.*)Opera(.*)/", "device_name" => USER_AGENT_OPERA_MOBILE),
      array("pattern" => "/MSIE(.*)Symbian OS(.*)Opera(.*)/", "device_name" => USER_AGENT_OPERA_MOBILE),
      array("pattern" => "/Opera/", "device_name" => USER_AGENT_OPERA),
      array("pattern" => "/compatible(.*)MSIE/", "device_name" => USER_AGENT_IE),
      array("pattern" => "/compatible(.*)Konqueror/", "device_name" => USER_AGENT_KONQUEROR),
    );
    
    foreach ($known_user_agents as $known_user_agent) {
    	if (preg_match($known_user_agent["pattern"], $user_agent)) {
    	  define('USER_AGENT', $known_user_agent['device_name']);
    	  return null;
    	} // if
    } // foreach
    
    if (!defined('USER_AGENT')) {
      define('USER_AGENT', USER_AGENT_DEFAULT);
    } // if
    
  } // get_user_agent
  
  get_user_agent();
  
  /**
   * Check is @user_agent is mobile device
   *
   * @param string $user_agent
   * @return boolean
   */
  function is_mobile_device($user_agent) {
    return in_array($user_agent,array(
      USER_AGENT_IPHONE,
      USER_AGENT_IPOD_TOUCH,
      USER_AGENT_SYMBIAN,
      USER_AGENT_OPERA_MINI,
      USER_AGENT_ANDROID,
      USER_AGENT_BLACKBERRY,
      USER_AGENT_MOBILE_IE,
      USER_AGENT_OPERA_MOBILE
    ));
  } // is_mobile_device
  
  // ---------------------------------------------------
  //  HTML generators
  // ---------------------------------------------------
  
  /**
   * Open HTML tag
   *
   * @param string $name Tag name
   * @param array $attributes Array of tag attributes
   * @param boolean $empty If tag is empty it will be automaticly closed
   * @return string
   */
  function open_html_tag($name, $attributes = null, $empty = false) {
    $attribute_string = '';
    if(is_array($attributes) && count($attributes)) {
      $prepared_attributes = array();
      foreach($attributes as $k => $v) {
        if(trim($k) <> '') {
          
          if(is_bool($v)) {
            if($v) $prepared_attributes[] = "$k=\"$k\"";
          } else {
            $prepared_attributes[] = $k . '="' . clean($v) . '"';
          } // if
          
        } // if
      } // foreach
      $attribute_string = implode(' ', $prepared_attributes);
    } // if
    
    $empty_string = $empty ? ' /' : ''; // Close?
    return "<$name $attribute_string$empty_string>"; // And done...
  } // html_tag
  
  /**
   * Render form label element. This helper makes it really simple to mark reqired elements
   * in a standard way
   *
   * @param string $text Label content
   * @param string $for ID of related elementet
   * @param boolean $is_required Mark as a required fiedl
   * @param array $attributes Additional attributes
   * @param string $after_label Label text sufix
   * @return null
   */
  function label_tag($text, $for = null, $is_required = false, $attributes = null, $after_label = ':') {
    if(trim($for)) {
      if(is_array($attributes)) {
        $attributes['for'] = trim($for);
      } else {
        $attributes = array('for' => trim($for));
      } // if
    } // if
    
    $render_text = trim($text) . $after_label;
    if($is_required) {
      $render_text .= ' <span class="label_required">*</span>';
    } // if
    
    return open_html_tag('label', $attributes) . $render_text . '</label>';
  } // label_tag
  
  /**
   * Render radio field
   *
   * @param string $name Field name
   * @param mixed $value
   * @param boolean $checked
   * @param array $attributes Additional attributes
   * @return string
   */
  function radio_field($name, $checked = false, $attributes = null) {
    if(is_array($attributes)) {
      $attributes['type'] = 'radio';
      if(!isset($attributes['class'])) {
        $attributes['class'] = 'inline';
      } // if
    } else {
      $attributes = array('type' => 'radio', 'class' => 'inline');
    } // if
    
    // Value
    $value = array_var($attributes, 'value', false);
    if($value === false) {
      $value = 'checked';
    } // if
    
    // Checked
    if($checked) {
      $attributes['checked'] = 'checked';
    } else {
      if(isset($attributes['checked'])) {
        unset($attributes['checked']);
      } // if
    } // if
    
    $attributes['name'] = $name;
    $attributes['value'] = $value;
    
    return open_html_tag('input', $attributes, true);
  } // radio_field
  
  /**
   * Render select list box
   * 
   * Options is array of already rendered option and optgroup tags
   *
   * @param array $options Array of already rendered option and optgroup tags
   * @param array $attributes Additional attributes
   * @return null
   */
  function select_box($options, $attributes = null) {
    $output = open_html_tag('select', $attributes) . "\n";
    if(is_array($options)) {
      foreach($options as $option) {
        $output .= $option . "\n";
      } // foreach
    } // if
    return $output . '</select>' . "\n";
  } // select_box
  
  /**
   * Render option tag
   *
   * @param string $text Option text
   * @param mixed $value Option value
   * @param array $attributes
   * @return string
   */
  function option_tag($text, $value = null, $attributes = null) {
    if(!is_null($value)) {
      if(is_array($attributes)) {
        $attributes['value'] = $value;
      } else {
        $attributes = array('value' => $value);
      } // if
    } // if
    return open_html_tag('option', $attributes) . clean($text) . '</option>';
  } // option_tag
  
  /**
   * Render option group
   *
   * @param string $label Group label
   * @param array $options
   * @param array $attributes
   * @return string
   */
  function option_group_tag($label, $options, $attributes = null) {
    if(is_array($attributes)) {
      $attributes['label'] = $label;
    } else {
      $attributes = array('label' => $label);
    } // if
    
    $output = open_html_tag('optgroup', $attributes) . "\n";
    if(is_array($options)) {
      foreach($options as $option) {
        $output .= $option . "\n";
      } // foreach
    } // if
    return $output . '</optgroup>' . "\n";
  } // option_group_tag
  
  /**
   * Extend url with additional parameters
   *
   * @param string $url
   * @param array $extend_with
   * @return string
   */
  function extend_url($url, $extend_with) {
    if (!$url || !is_foreachable($extend_with)) {
      return $url;
    } // if
    
    $extended_url = $url;
    foreach ($extend_with as $extend_element_key => $extend_element_value) {
      if (strpos($extended_url,  '?') === false) {
        $extended_url.= '?';
      } else {
        $extended_url.= '&';
      } // if
      $extended_url.= ($extend_element_key.'='.$extend_element_value);
    } // foreach
    
    return $extended_url;
  } // extend_url

?>