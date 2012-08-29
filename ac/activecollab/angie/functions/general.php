<?php

  /**
   * General purpose functions
   * 
   * This file contains various general purpose functions used for string and 
   * array manipulation, input filtering, ouput cleaning end so on.
   *
   * @package angie.functions
   */
  

  /**
   * Sort an array by key
   *
   * @param Array to be sorted $array
   * @param Key by which the array is going to be sorted $sortByKey
   * @param Order by asc/desc $order
   * @param Order type $type
   * @return Sorted array
   */
  function sortByKey($array, $sortByKey, $order = 'asc', $type = SORT_STRING) {
    foreach ($array as $key=>$value) {
      $temp[$key] = $value[$sortByKey];
	  }
	  
	  $order == 'asc' ? asort($temp, $type) : arsort($temp, $type);
	
	  foreach ($temp as $key=>$value) {
	   $sortedArray[] = $array[$key];
	  }
    
	  return is_array($sortedArray) ? $sortedArray : array();
  } // sort by key
  
  
  /**
   * Make links clickable
   *
   * @param string $text
   * @return string
   */
  function make_links_clickable($text) {    
    $text = " ".$text;
    // something://else
    $text = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);
    // www/ftp.something.com
    $text = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);
    // myemail@website.com
    $text = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $text);
    
    return substr($text, 1);
  }
  
  /**
   * Strip specific tags from a string
   * Usage: stripTags($string, 'a,p,div')
   *
   * @internal dedicated to Danko, my dear love
   * @param string $string
   * @param $tagsToRemove comma-separated list of tags
   * @return string
   */
  function stripSpecificTags($string, $tagsToRemove = '') {
    $tags = explode(",",$tagsToRemove);
    foreach ($tags as $tag) {
      $tag = trim($tag);
      $string = preg_replace('/<' . $tag . '[^>]*>/i', '', $string);
      $string = preg_replace('/<\/' . $tag . '[^>]*>/i', '', $string);
    }

    return $string;
  }
  
  /**
   * Check if $var is object of $class_name
   *
   * @param mixed $var
   * @param string $class_name
   * @return boolean
   */
  function instance_of($var, $class_name) {
    return is_object($var) && is_a($var, $class_name);
  } // instance_of

  /**
   * This function will return true only if input string starts with
   * niddle
   *
   * @param string $string Input string
   * @param string $niddle Needle string
   * @return boolean
   */
  function str_starts_with($string, $niddle) {  
  	return substr($string, 0, strlen($niddle)) == $niddle;  	
  } // end func str_starts with
  
  /**
   * This function will return true only if input string ends with
   * niddle
   *
   * @param string $string Input string
   * @param string $niddle Needle string
   * @return boolean
   */
  function str_ends_with($string, $niddle) {
    return substr($string, strlen($string) - strlen($niddle), strlen($niddle)) == $niddle;
  } // end func str_ends_with
  
  /**
   * Return begining of the string
   *
   * @param string $string
   * @param integer $lenght
   * @param string $etc
   * @return string
   */
  function str_excerpt($string, $lenght = 100, $etc = '...') {
    $strlen = strlen_utf($string);
    return $strlen <= $lenght ? $string : substr_utf($string, 0, $lenght) . $etc;
  } // str_excerpt
  
  /**
   * Parse encoded string and return array of parameters
   *
   * @param string $str
   * @return array
   */
  function parse_string($str) {
    $result = null;
    parse_str($str, $result);
    return $result;
  } // parse_string
  
  // str_ireplace implementation
  if (!function_exists('str_ireplace')) {

    /**
     * Replace str_ireplace()
     *
     * This function does not support the $count argument because
     * it cannot be optional in PHP 4 and the performance cost is
     * too great when a count is not necessary.
     *
     * @category    PHP
     * @package     PHP_Compat
     * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
     * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
     * @link        http://php.net/function.str_ireplace
     * @author      Aidan Lister <aidan@php.net>
     * @author      Arpad Ray <arpad@php.net>
     * @version     $Revision: 1.24 $
     * @since       PHP 5
     * @require     PHP 4.0.0 (user_error)
     */
    function str_ireplace($search, $replace, $subject) {
      // Sanity check
      if (is_string($search) && is_array($replace)) {
        user_error('Array to string conversion', E_USER_NOTICE);
        $replace = (string) $replace;
      }

      // If search isn't an array, make it one
      $search = (array) $search;
      $length_search = count($search);

      // build the replace array
      $replace = is_array($replace) ? array_pad($replace, $length_search, '') : array_pad(array(), $length_search, $replace);

      // If subject is not an array, make it one
      $was_string = false;
      if(is_string($subject)) {
        $was_string = true;
        $subject = array ($subject);
      }

      // Prepare the search array
      foreach($search as $search_key => $search_value) {
        $search[$search_key] = '/' . preg_quote($search_value, '/') . '/i';
      }

      // Prepare the replace array (escape backreferences)
      $replace = str_replace(array('\\', '$'), array('\\\\', '\$'), $replace);

      $result = preg_replace($search, $replace, $subject);
      return $was_string ? $result[0] : $result;
    } // str_ireplace
    
  } // if
  
  /**
   * Better nl2br that preserves newlines inside <pre> and <code> blocks
   *
   * @param string $string
   * @return string
   */
  function nl2br_pre($string) {
    $string = nl2br($string);
    $string =  preg_replace('/<pre>(.*?)<\/pre>/ise',"'<pre>' . preg_replace('/(<br \/?>)/is','','\\1') . '</pre>'",$string);
    $string =  preg_replace('/<code>(.*?)<\/code>/ise',"'<code>' . preg_replace('/(<br \/?>)/is','','\\1') . '</code>'",$string);
    return $string;
  } // nl2br_pre
  
  if(!function_exists('http_build_query')) {
    
    /**
      * Generates a URL-encoded query string from the associative (or indexed) array provided.
      *
      * @param array $data
      * @param string $prefix
      * @param string $sep
      * @param string $key
      * @return string
      */
    function http_build_query($data, $prefix = null, $sep = '', $key = '') {
      $ret = array();
      foreach((array)$data as $k => $v) {
        $k = urlencode($k);
        if(is_int($k) && $prefix != null) {
          $k = $prefix.$k;
        } // if
        if(!empty($key)) {
          $k = $key."[".$k."]";
        } // if

        if(is_array($v) || is_object($v)) {
          array_push($ret,http_build_query($v,"",$sep,$k));
        } else {
          array_push($ret,$k."=".urlencode($v));
        } // if
      } // foreach

      if(empty($sep)) {
        $sep = ini_get("arg_separator.output");
      } // if

      return implode($sep, $ret);
    } // http_build_query
  } // if
  
  /**
   * convert backslashes to slashes
   *
   * @param string $path
   */
  function fix_slashes($path) {
    return str_replace("\\", "/", $path);
  } // fix_slashes
  
  /**
   * Return path with trailing slash
   *
   * @param string $path Input path
   * @return string Path with trailing slash
   */
  function with_slash($path) {
    return str_ends_with($path, '/') ? $path : $path . '/';
  } // end func with_slash
  
  /**
   * Remove trailing slash from the end of the path (if exists)
   *
   * @param string $path File path that need to be handled
   * @return string
   */
  function without_slash($path) {
    return str_ends_with($path, '/') ? substr($path, 0, strlen($path) - 1) : $path;
  } // without_slash
  
  /**
   * Replace first $search_for with $replace_with in $in. If $search_for is not found
   * original $in string will be returned...
   *
   * @param string $search_for Search for this string
   * @param string $replace_with Replace it with this value
   * @param string $in Haystack
   * @return string
   */
  function str_replace_first($search_for, $replace_with, $in) {
    $pos = strpos($in, $search_for);
    if($pos === false) {
      return $in;
    } else {
      return substr($in, 0, $pos) . $replace_with . substr($in, $pos + strlen($search_for), strlen($in));
    } // if
  } // str_replace_first
  
  /**
   * Make random string
   *
   * @param integer $length
   * @param string $allowed_chars
   * @return string
   */
  function make_string($length = 10, $allowed_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890') {
    $result = '';
    $allowed_chars_len = strlen($allowed_chars);
    
    while(strlen($result) < $length) {
      $result .= substr($allowed_chars, rand(0, $allowed_chars_len), 1);
    } // for
    
    return $result;
  } // make_string
  
  /**
   * Make a passsword out of list of allowed characters with a given length
   * 
   * Difference between make_string and make_password is in the list of allowed 
   * chars. Some chars that create a lot of confusion (I, l and so on) are 
   * excluded in password generation function
   *
   * @param integer $length
   * @param string $allowed_chars
   * @return string
   */
  function make_password($length = 10, $allowed_chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789') {
    return make_string($length, $allowed_chars);
  } // make_password
  
  /**
   * Return formatted float
   * 
   * This function will remove trailing zeros and dot if we have X.00 result
   *
   * @param float $value
   * @param integer $decimals
   * @return string
   */
  function float_format($value, $decimals = 2) {
    $result = number_format($value, $decimals, NUMBER_FORMAT_DEC_SEPARATOR, NUMBER_FORMAT_THOUSANDS_SEPARATOR);
    if(strpos($result, NUMBER_FORMAT_DEC_SEPARATOR) === false) {
      return $result;
    } else {
      return trim(trim($result, '0'), '.');
    } // if
  } // float_format
  
  /**
   * Prepare HTML before saving it into database
   *
   * @param string $value
   * @param boolean $purify
   * @return string
   */
  function prepare_html($value, $purify = false) {
    require_once ANGIE_PATH . '/classes/htmlpurifier/init.php';
        
    $value = trim($value);
    if($value != '') {
      // Remove brs from the end of the string
      if(str_ends_with($value, '<br /><br />')) {
        $value = substr_utf($value, 0, strlen_utf($value) - 12);
      } // if
              
      if($purify) {
        $value = purify_html($value);
      } // if
      
      // Clean up Microsoft Office paste:
      // <p> &lt;!--  /* Font Definitions */--&gt;  </p>
      if(str_starts_with($value, '<p> &lt;!--  /* Font Definitions */')) {
        $value = preg_replace('/(<p>)[\s]+(\&lt;\!--)[\s]+(\/\*)[\s]+(Font)[\s]+(Definitions)[\s]+(\*\/)(.*)(--\&gt\;)[\s]+(<\/p>)/i', '', $value);
      } // if
      
      return str_replace(array('<br>', '<br/>', '<br />'), array("\n", "\n", "\n"), $value);
    } // if
    
    return '';
  } // prepare_html
  
  /**
   * Convert $html from HTML to plain text
   *
   * @param string $html
   * @return string
   */
  function html_to_text($html) {
    $search = array("/\r/", "/[\n\t]+/", '/[ ]{2,}/', '/<script[^>]*>.*?<\/script>/i', '/<style[^>]*>.*?<\/style>/i', '/<h[123][^>]*>(.*?)<\/h[123]>/ie', '/<h[456][^>]*>(.*?)<\/h[456]>/ie', '/<p[^>]*>/i', '/<br[^>]*>/i', '/<b[^>]*>(.*?)<\/b>/ie', '/<strong[^>]*>(.*?)<\/strong>/ie','/<i[^>]*>(.*?)<\/i>/i', '/<em[^>]*>(.*?)<\/em>/i', '/(<ul[^>]*>|<\/ul>)/i', '/(<ol[^>]*>|<\/ol>)/i', '/<li[^>]*>(.*?)<\/li>/i', '/<li[^>]*>/i', '/<a [^>]*href="([^"]+)"[^>]*>(.*?)<\/a>/ie', '/<hr[^>]*>/i', '/(<table[^>]*>|<\/table>)/i', '/(<tr[^>]*>|<\/tr>)/i', '/<td[^>]*>(.*?)<\/td>/i', '/<th[^>]*>(.*?)<\/th>/ie', '/&(nbsp|#160);/i', '/&(quot|rdquo|ldquo|#8220|#8221|#147|#148);/i', '/&(apos|rsquo|lsquo|#8216|#8217);/i', '/&gt;/i', '/&lt;/i', '/&(amp|#38);/i', '/&(copy|#169);/i', '/&(trade|#8482|#153);/i', '/&(reg|#174);/i', '/&(mdash|#151|#8212);/i', '/&(ndash|minus|#8211|#8722);/i', '/&(bull|#149|#8226);/i', '/&(pound|#163);/i', '/&(euro|#8364);/i', '/&[^&;]+;/i', '/[ ]{2,}/');
    $replace = array('', ' ', ' ', '', '',  "strtoupper(\"\n\n\\1\n\n\")", "ucwords(\"\n\n\\1\n\n\")", "\n\n\t", "\n", 'strtoupper("\\1")', 'strtoupper("\\1")', '_\\1_', '_\\1_', "\n\n", "\n\n", "* \\1\n", "\n* ", 'html_to_text_process_url("\\1", "\\2")', "\n-------------------------\n", "\n\n", "\n", "\t\t\\1\n", "strtoupper(\"\t\t\\1\n\")", ' ', '"', "'", '>', '<', '&', '(c)', '(tm)', '(R)', '--', '-', '*','�','EUR', '', ' ');
    
    $text = trim(stripslashes($html));
    $text = preg_replace($search, $replace, $text);
    $text = strip_tags($text);

    $text = preg_replace("/\n\s+\n/", "\n\n", $text);
    $text = preg_replace("/[\n]{3,}/", "\n\n", $text);

    return trim($text);
  } // html_to_text
  
  /**
   * Convert $html from HTML to plain email
   *
   * @param string $html
   * @return string
   */
  function html_to_plain_email($html) {
    $search = array("/\r/", "/[\n\t]+/", '/[ ]{2,}/', '/<script[^>]*>.*?<\/script>/i', '/<style[^>]*>.*?<\/style>/i', '/<h[123][^>]*>(.*?)<\/h[123]>/ie', '/<h[456][^>]*>(.*?)<\/h[456]>/ie', '/<p[^>]*>/i', '/<br[^>]*>/i', '/<b[^>]*>(.*?)<\/b>/ie', '/<strong[^>]*>(.*?)<\/strong>/ie','/<i[^>]*>(.*?)<\/i>/i', '/<em[^>]*>(.*?)<\/em>/i', '/(<ul[^>]*>|<\/ul>)/i', '/(<ol[^>]*>|<\/ol>)/i', '/<li[^>]*>(.*?)<\/li>/i', '/<li[^>]*>/i', '/<a [^>]*href="([^"]+)"[^>]*>(.*?)<\/a>/ie', '/<hr[^>]*>/i', '/(<table[^>]*>|<\/table>)/i', '/(<tr[^>]*>|<\/tr>)/i', '/<td[^>]*>(.*?)<\/td>/i', '/<th[^>]*>(.*?)<\/th>/ie', '/&(nbsp|#160);/i', '/&(quot|rdquo|ldquo|#8220|#8221|#147|#148);/i', '/&(apos|rsquo|lsquo|#8216|#8217);/i', '/&gt;/i', '/&lt;/i', '/&(amp|#38);/i', '/&(copy|#169);/i', '/&(trade|#8482|#153);/i', '/&(reg|#174);/i', '/&(mdash|#151|#8212);/i', '/&(ndash|minus|#8211|#8722);/i', '/&(bull|#149|#8226);/i', '/&(pound|#163);/i', '/&(euro|#8364);/i', '/&[^&;]+;/i', '/[ ]{2,}/');
    $replace = array('', ' ', ' ', '', '',  "strtoupper(\"\n\n\\1\n\n\")", "ucwords(\"\n\n\\1\n\n\")", "\n\n\t", "\n", 'strtoupper("\\1")', 'strtoupper("\\1")', '_\\1_', '_\\1_', "\n\n", "\n\n", "* \\1\n", "\n* ", 'html_to_text_process_url("\\1", "\\2")', "\n-------------------------\n", "\n\n", "\n", "\t\t\\1\n", "strtoupper(\"\t\t\\1\n\")", ' ', '"', "'", '>', '<', '&', '(c)', '(tm)', '(R)', '--', '-', '*','�','EUR', '', ' ');
    
    $text = trim(stripslashes($html));
    $text = preg_replace($search, $replace, $text);
    $text = strip_tags($text, '<blockquote>');

    $text = preg_replace("/\n\s+\n/", "\n\n", $text);
    $text = preg_replace("/[\n]{3,}/", "\n\n", $text);

    return trim($text);
  } // html_to_plain_email
  
  /**
   * This function is used as a callback in html_to_text function to process 
   * links found in the text
   *
   * @param string $url
   * @param string $text
   * @return string
   */
  function html_to_text_process_url($url, $text) {
    if(str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
      return "$text [$url]";
    } elseif(str_starts_with($url, 'mailto:')) {
      return $text . ' [' . substr($url, 7) . ']';
    } else {
      return $text;
    } // if
  } // html_to_text_process_url
  
  // ---------------------------------------------------
  //  Input validation
  // ---------------------------------------------------
  
  /**
   * Check if selected email has valid email format
   *
   * @param string $user_email Email address
   * @return boolean
   */
  function is_valid_email($user_email) {
    if(strstr($user_email, '@') && strstr($user_email, '.')) {
    	return (boolean) preg_match(EMAIL_FORMAT, $user_email);
    } else {
    	return false;
    } // if
  } // end func is_valid_email
  
  /**
   * Verify the syntax of the given URL.
   * 
   * - samples
   *    http://127.0.0.1 : valid
   *    http://pero_mara.google.com : valid
   *    http://pero-mara.google.com : valid
   *    https://pero-mara.goo-gle.com/something : valid
   *    http://pero-mara.goo_gle.com/~we_use : valid
   *    http://www.google.com : valid
   *    http://activecollab.dev : valid
   *    http://127.0.0.1/~something : valid
   *    http://127.0.0.1/something : valid
   *    http://333.0.0.1 : invalid
   *    http://dev : invalid
   *    .dev : invalid
   *    activecollab.dev : invalid
   *    http://something : invalid
   *    http://127.0 : invalid
   *
   * @param $url The URL to verify.
   * @return boolean
   */
  function is_valid_url($url) {
    if(str_starts_with(strtolower($url), 'http://localhost')) {
      return true;
    } // if
    
    if (preg_match(IP_URL_FORMAT, $url)) {
      return true;
    } // if
    
    return preg_match(URL_FORMAT, $url);
  } // is_valid_url 
  
  /**
   * verify that given string is valid ip address
   *
   * @param string $ip_address
   * @return boolean
   */
  function is_valid_ip_address($ip_address) {
    if (preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ip_address)) {
      return true;
    } // if
    return false;
  } // is_valid_ip_address
  
  /**
   * This function will return true if $str is valid function name (made out of alpha numeric characters + underscore)
   *
   * @param string $str
   * @return boolean
   */
  function is_valid_function_name($str) {
    $check_str = trim($str);
    if($check_str == '') {
      return false; // empty string
    } // if
    
    $first_char = substr_utf($check_str, 0, 1);
    if(is_numeric($first_char)) {
      return false; // first char can't be number
    } // if
    
    return (boolean) preg_match("/^([a-zA-Z0-9_]*)$/", $check_str);
  } // is_valid_function_name
  
  /**
   * Check if specific string is valid hash. Lenght is not checked!
   *
   * @param string $hash
   * @return boolean
   */
  function is_valid_hash($hash) {
    return preg_match("/^([a-f0-9]*)$/", $hash);
  } // is_valid_hash
  
  // ---------------------------------------------------
  //  Cleaning
  // ---------------------------------------------------
  
  /**
   * This function will return clean variable info
   *
   * @param mixed $var
   * @param string $indent Indent is used when dumping arrays recursivly
   * @param string $indent_close_bracet Indent close bracket param is used
   *   internaly for array output. It is shorter that var indent for 2 spaces
   * @return null
   */
  function clean_var_info($var, $indent = '&nbsp;&nbsp;', $indent_close_bracet = '') {
    if(is_object($var)) {
      return 'Object (class: ' . get_class($var) . ')';
    } elseif(is_resource($var)) {
      return 'Resource (type: ' . get_resource_type($var) . ')';
    } elseif(is_array($var)) {
      $result = 'Array (';
      if(count($var)) {
        foreach($var as $k => $v) {
          $k_for_display = is_integer($k) ? $k : "'" . clean($k) . "'";
          $result .= "\n" . $indent . '[' . $k_for_display . '] => ' . clean_var_info($v, $indent . '&nbsp;&nbsp;', $indent_close_bracet . $indent);
        } // foreach
      } // if
      return $result . "\n$indent_close_bracet)";
    } elseif(is_int($var)) {
      return '(int)' . $var;
    } elseif(is_float($var)) {
      return '(float)' . $var;
    } elseif(is_bool($var)) {
      return $var ? 'true' : 'false';
    } elseif(is_null($var)) {
      return 'NULL';
    } else {
      return "(string) '" . clean($var) . "'";
    } // if
  } // clean_var_info
  
  /**
   * Equivalent to htmlspecialchars(), but allows &#[0-9]+ (for unicode)
   * 
   * This function was taken from punBB codebase <http://www.punbb.org/>
   *
   * @param string $str
   * @return string
   */
  function clean($str) {
    $str = preg_replace('/&(?!#(?:[0-9]+|x[0-9A-F]+);?)/si', '&amp;', $str);
  	$str = str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $str);
  
  	return $str;
  } // clean
  
  /**
   * Convert entities back to valid characteds
   *
   * @param string $escaped_string
   * @return string
   */
  function undo_htmlspecialchars($escaped_string) {
    $search = array('&amp;', '&lt;', '&gt;', '&quot;');
    $replace = array('&', '<', '>', '"');
    return str_replace($search, $replace, $escaped_string);
  } // undo_htmlspecialchars
  
  // ---------------------------------------------------
  //  Object handling function
  // ---------------------------------------------------
  
  /**
   * Populate object properties from array through setter
   * 
   * This function will loop through $array, prepare setter based on element key and 
   * if setter exists and is not protected it will be called with elements value as 
   * first parametar.
   *
   * @param object $object
   * @param array $array
   * @param array $protected_methods
   * @return null
   */
  function populate_through_setter($object, $array, $protected_methods = null) {
    if(!is_object($object)) {
      return;
    } // if
    
    if(is_foreachable($array)) {
      $object_methods = get_class_methods(get_class($object));
      $protected_methods = is_array($protected_methods) ? $protected_methods : array();
      
      foreach($array as $property_name => $property_value) {
        $setter = 'set' . Angie_Inflector::camelize($property_name);
        if(in_array($setter, $object_methods) && !in_array($setter, $protected_methods)) {
          $object->$setter($property_value);
        } // if
      } // foreahc
    } // if
  } // populate_through_setter
  
  // ---------------------------------------------------
  //  Array handling functions
  // ---------------------------------------------------
  
  /**
   * Is $var foreachable
   * 
   * This function will return true if $var is array and it is not empty
   *
   * @param mixed $var
   * @return boolean
   */
  function is_foreachable($var) {
    return !empty($var) && is_array($var);
  } // is_foreachable
  
  /**
   * Return variable from an array
   * 
   * If field $name does not exists in array this function will return $default
   *
   * @param array $from Hash
   * @param string $name
   * @param mixed $default
   * @param boolean $and_unset
   * @return mixed
   */
  function array_var(&$from, $name, $default = null, $and_unset = false) {
    if(is_array($from) || (is_object($from) && instance_of($from, 'ArrayAccess'))) {
      if($and_unset) {
        if(array_key_exists($name, $from)) {
          $result = $from[$name];
          unset($from[$name]);
          return $result;
        } // if
      } else {
        return array_key_exists($name, $from) ? $from[$name] : $default;
      } // if
    } // if
    return $default;
  } // array_var
  
  /**
   * Flattens the array
   * 
   * This function will walk recursivly throug $array and all array values will be appended to $array and removed from
   * subelements. Keys are not preserved (it just returns array indexed form 0 .. count - 1)
   *
   * @param array $array If this value is not array it will be returned as one
   * @return array
   */
  function array_flat($array) {
    if(!is_array($array)) {
      return array($array);
    } // if
    
    $result = array();
    
    foreach($array as $value) {
      if(is_array($value)) {
        $value = array_flat($value);
        foreach($value as $subvalue) {
          $result[] = $subvalue;
        } // if
      } else {
        $result[] = $value;
      } // if
    } // if
    
    return $result;
  } // array_flat
  
  /**
   * This function will return $str as an array
   *
   * @param string $str
   * @return array
   */
  function string_to_array($str) {
    if(!is_string($str) || (strlen($str) == 0)) {
      return array();
    } // if
    
    $result = array();
    for($i = 0, $strlen = strlen($str); $i < $strlen; $i++) {
      $result[] = $str[$i];
    } // if
    
    return $result;
  } // string_to_array
  
  /**
   * Extract results of specific method from an array of objects
   * 
   * This method will go through all items of an $array and call $method. Results will be agregated into one array that 
   * will be returned. If $check_if_method_exists is set to true than additional checks will be done on the object 
   * (slower but safer). $check_if_method_exists is Off by default.
   * 
   * If $preserve_keys is true keys will be preserved in the resulting array...
   *
   * @param array $array
   * @param string $method
   * @param array $arguments
   * @param boolean $preserve_keys
   * @param boolean $check_if_method_exists
   * @return array
   */
  function objects_array_extract($array, $method, $arguments = null, $preserve_keys = false, $check_if_method_exists = false) {
    if(!is_array($array)) {
      return null;
    } // if
    
    $results = array();
    foreach($array as $key => $element) {
      $element =& $array[$key];
      
      $call = array($element, $method);
      if(is_callable($call, false)) {
        if(is_array($arguments)) {
          $result = call_user_func_array($call, $arguments);
        } elseif(is_string($arguments)) {
          $result = call_user_func($call, $arguments);
        } else {
          $result = call_user_func($call);
        } // if
        
        if($preserve_keys) {
          $results[$key] = $result;
        } else {
          $results[] = $result;
        } // if
        
      } // if
    } // foreach
    return $results;
  } // objects_array_extract
  
  /**
   * Array to CSV
   * 
   * Every $array record is an array of values that need to be exported in CSV
   *
   * @param array $array
   * @return string
   */
  function array_to_csv($array) {
  	if(!is_array($array)){
  	  return null;
  	} // if
  	
  	$result = array();
  	foreach($array as $value_set) {
  	  $values = array();
  	  
  	  foreach($value_set as $value) {
  	    $value = str_replace('"', '""', $value);
  	  
  	    if(strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false || strpos($value, "\r") !== false) {
  		  	$values[] = '"' . $value . '"';
  		  } else {
  		    $values[] = $value;
  		  } // if
  	  } // foreach
  	  
  	  $result[] = implode(',', $values);
  	} // foreach
  	
  	return implode("\n", $result);
  } // array_to_csv
  
  /**
   * Returns first element of an array
   * 
   * If $key is true first key will be returned, value otherwise.
   *
   * @param array $arr
   * @param boolean $key
   * @return mixed
   */
  function first($arr, $key = false) {
    foreach($arr as $k => $v) {
      return $key ? $k : $v;
    } // foreach
  } // first
  
  // ---------------------------------------------------
  //  Misc functions
  // ---------------------------------------------------
  
  /**
   * Show var dump. pre_var_dump() is used for testing only!
   *
   * @param mixed $var
   * @return null
   */
  function pre_var_dump($var) {
    print "<pre style=\"text-align: left\">\n";
    
    ob_start();
    var_dump($var);
    print clean(ob_get_clean());
    
    print "</pre>\n";
  } // pre_var_dump
  
  /**
   * Return max upload size
   * 
   * This function will check for max upload size and return value in bytes. By default it will compare values of 
   * upload_max_filesize and post_max_size from php.ini, but it can also take additional values provided as arguments 
   * (for instance, if you store data in MySQL database one of the limiting factors can be max_allowed_packet 
   * configuration value). 
   * 
   * Examples:
   * <pre>
   * $max_size = get_max_upload_size(); // check only data from php.ini
   * $max_size = get_max_upload_size(12000, 18000); // take this values into calculation too
   * </pre>
   *
   * @param mixed
   * @return integer
   */
  function get_max_upload_size() {
    $arguments = func_get_args();
    if(!is_array($arguments)) {
      $arguments = array();
    } // if
    
    $arguments[] = php_config_value_to_bytes(ini_get('upload_max_filesize'));
    $arguments[] = php_config_value_to_bytes(ini_get('post_max_size'));
    
    $min = null;
    foreach($arguments as $argument) {
      if(is_null($min)) {
        $min = $argument;
      } else {
        $min = min($argument, $min);
      } // if
    } // if
    
    return $min;
  } // get_max_upload_size
  
  /**
   * Convert filesize value from php.ini to bytes
   * 
   * Convert PHP config value (2M, 8M, 200K...) to bytes. This function was taken from PHP documentation. $val is string 
   * value that need to be converted
   *
   * @param string $val
   * @return integer
   */
  function php_config_value_to_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
      // The 'G' modifier is available since PHP 5.1.0
      case 'g':
        $val *= 1024;
      case 'm':
        $val *= 1024;
      case 'k':
        $val *= 1024;
    } // if
    
    return $val;
  } // php_config_value_to_bytes
  
  /**
   * This function will return request string relative to dispatch file
   *
   * @param void
   * @return stirng
   */
  function get_request_string() {
    return substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['PHP_SELF'])));
  } // get_request_string
  
  /**
   * Compare $value1 and $value2 with $comparision and return boolean result
   * 
   * Examples:
   * <pre>
   * is_true_statement(1, COMPARE_EQ, 1); // true
   * is_true_statement(1, COMPARE_EQ, 3); // false
   * </pre>
   *
   * @param mixed $value1
   * @param string $comparision
   * @param mixed $value2
   * @return boolean
   */
  function is_true_statement($value1, $comparision = COMPARE_EQ, $value2) {
    switch($comparision) {
      case COMPARE_LT:
        if($value1 < $value2) {
          return true;
        } // if
        break;
      case COMPARE_LE:
        if($value1 <= $value2) {
          return true;
        } // if
        break;
      case COMPARE_GT:
        if($value1 > $value2) {
          return true;
        } // if
        break;
      case COMPARE_GE:
        if($value1 >= $value2) {
          return true;
        } // if
        break;
      case COMPARE_EQ:
        if($value1 == $value2) {
          return true;
        } // if
        break;
      case COMPARE_NE:
        if($value1 != $value2) {
          return true;
        } // if
        break;
    } // switch
    return false;
  } // is_true_statement
  
  // ---------------------------------------------------
  //  Image management
  // ---------------------------------------------------
  
  /**
   * Open image file
   * 
   * This function will try to open image file
   *
   * @param string $file
   * @return resource
   */
  function open_image($file) {
    if(!extension_loaded('gd')) {
      return false;
    } // if
    
    $info =& getimagesize($file);
    if($info) {
      switch($info[2]) {
        case IMAGETYPE_JPEG:
          return array(
            'type' => IMAGETYPE_JPEG,
            'resource' => imagecreatefromjpeg($file)
          ); // array
        case IMAGETYPE_GIF:
          return array(
            'type' => IMAGETYPE_GIF,
            'resource' => imagecreatefromgif($file)
          ); // array
        case IMAGETYPE_PNG:
          return array(
            'type' => IMAGETYPE_PNG,
            'resource' => imagecreatefrompng($file)
          ); // array
      } // switch
    } // if
    
    return null;
  } // open_image
  
  /**
   * Resize input image
   *
   * @param string $input_file
   * @param string $dest_file
   * @param integer $max_width
   * @param integer $max_height
   * @return boolean
   */
  function scale_image($input_file, $dest_file, $max_width, $max_height, $output_type = null, $quality = 80) {
    if(!extension_loaded('gd')) {
      return false;
    } // if
    
    $open_image = open_image($input_file);
    
    if(is_array($open_image)) {
      $image_type = $open_image['type'];
      $image = $open_image['resource'];
      
      $width  = imagesx($image);
      $height = imagesy($image);
      
      $scale  = min($max_width / $width, $max_height / $height);
      
      if($scale < 1) {
        $new_width  = floor($scale * $width);
        $new_height = floor($scale * $height);

        $tmp_img = imagecreatetruecolor($new_width, $new_height);
        $white_color = imagecolorallocate($tmp_img, 255, 255, 255);
        imagefill($tmp_img, 0, 0, $white_color); 
        imagecopyresampled($tmp_img, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($image);
        $image = $tmp_img;
      } else if ($scale > 1) {
        $tmp_img = imagecreatetruecolor($max_width, $max_height);
        $white_color = imagecolorallocate($tmp_img, 255, 255, 255);
        imagefill($tmp_img, 0, 0, $white_color);
        imagecopy($tmp_img, $image, round(($max_width - $width) / 2), round(($max_height - $height) / 2), 0, 0, $width, $height);
        imagedestroy($image);
        $image = $tmp_img;
      } // if
      
      if($output_type === null) {
        $output_type = $image_type;
      } // if
      
      switch($output_type) {
        case IMAGETYPE_JPEG:
          return imagejpeg($image, $dest_file, $quality);
        case IMAGETYPE_GIF:
          if(!function_exists('imagegif')) {
            return false; // If GD is compiled without GIF support
          } // if
          return imagegif($image, $dest_file);
        case IMAGETYPE_PNG:
          return imagepng($image, $dest_file);
      } // switch
    } // ifs
    return false;
  } // scale_image
  
  /**
   * Force resize
   *
   * @param string $input_file
   * @param string $dest_file
   * @param integer $width
   * @param integer $height
   * @param mixed $output_type
   * @return null
   */
  function resize_image($input_file, $dest_file, $new_width, $new_height, $output_type = null,$quality = 80) {
    if(!extension_loaded('gd')) {
      return false;
    } // if
    
    $open_image = open_image($input_file);
    
    if(is_array($open_image)) {
      $image_type = $open_image['type'];
      $image = $open_image['resource'];
      
      $width  = imagesx($image);
      $height = imagesy($image);

      $tmp_img = imagecreatetruecolor($new_width, $new_height);
      
      $white_color = imagecolorallocate($tmp_img, 255, 255, 255);
      imagefill($tmp_img, 0, 0, $white_color); 
      imagecopyresampled($tmp_img, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
      imagedestroy($image);
      $image = $tmp_img;
      
      if($output_type === null) {
        $output_type = $image_type;
      } // if
      
      switch($output_type) {
        case IMAGETYPE_JPEG:
          return imagejpeg($image, $dest_file, $quality);
        case IMAGETYPE_GIF:
          return imagegif($image, $dest_file);
        case IMAGETYPE_PNG:
          return imagepng($image, $dest_file);
      } // switch
    } // ifs
    return false;
  } // resize_image
  
  /**
   * check if hex color code is valid
   *
   * @param string $color_code
   * @return boolean
   */
  function is_valid_hex_color($color_code) {
    if ((isset($color_code) && strlen($color_code) !=6 && strlen($color_code) !=3) || (isset($color_code) && !preg_match("/^[A-F0-9]+$/i",$color_code))) {
      return false;
    } // if
    return true;
  } // is_valid_hex_color
  
  
  /**
   * Returns true if php is able to resize images
   *  
   * @param void
   * @return boolean 
   */
  function can_resize_images() {
  	return extension_loaded('gd');
  } // can_resize_images
  
  // ---------------------------------------------------
  //  Compatibility
  // ---------------------------------------------------
  
  if(!function_exists('debug_print_backtrace')) {
  
    /**
     * Replace debug_print_backtrace()
     *
     * @category    PHP
     * @package     PHP_Compat
     * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
     * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
     * @link        http://php.net/function.debug_print_backtrace
     * @author      Laurent Laville <pear@laurent-laville.org>
     * @author      Aidan Lister <aidan@php.net>
     * @version     $Revision: 1.6 $
     * @since       PHP 5
     * @require     PHP 4.3.0 (debug_backtrace)
     */
    function debug_print_backtrace() {
      
      // Get backtrace
      $backtrace = debug_backtrace();
  
      // Unset call to debug_print_backtrace
      array_shift($backtrace);
      if (empty($backtrace)) {
          return '';
      }
  
      // Iterate backtrace
      $calls = array();
      foreach ($backtrace as $i => $call) {
          if (!isset($call['file'])) {
              $call['file'] = '(null)';
          }
          if (!isset($call['line'])) {
              $call['line'] = '0';
          }
          $location = $call['file'] . ':' . $call['line'];
          $function = (isset($call['class'])) ?
              $call['class'] . (isset($call['type']) ? $call['type'] : '.') . $call['function'] :
              $call['function'];
  
          $params = '';
          if (isset($call['args'])) {
              $args = array();
              foreach ($call['args'] as $arg) {
                  if (is_array($arg)) {
                      $args[] = print_r($arg, true);
                  } elseif (is_object($arg)) {
                      $args[] = get_class($arg);
                  } else {
                      $args[] = $arg;
                  }
              }
              $params = implode(', ', $args);
          }
  
          $calls[] = sprintf('#%d  %s(%s) called at [%s]',
              $i,
              $function,
              $params,
              $location);
      }
  
      echo implode("\n", $calls), "\n";
    } // debug_print_backtrace

  } // if


?>