<?php
  
  // ---------------------------------------------------
  //  Templates
  // ---------------------------------------------------
  
  /**
  * Return full path of specific template file
  *
  * @param string $tpl_file
  * @return string
  */
  function get_template_path($tpl_file) {
    return INSTALLER_PATH . '/templates/' . $tpl_file;
  } // get_template_path
  
  // ---------------------------------------------------
  //  Global functions
  // ---------------------------------------------------
  
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
   * Return variable from an array
   * 
   * If field $name does not exists in array this function will return $default
   *
   * @param array $from Hash
   * @param string $name
   * @param mixed $default
   * @return mixed
   */
  function array_var($from, $name, $default = null) {
    if(is_array($from)) {
      return isset($from[$name]) ? $from[$name] : $default;
    } elseif(is_object($from) && instance_of($from, 'ArrayAccess')) {
      return isset($from[$name]) ? $from[$name] : $default;
    } // if
    return $default;
  } // array_var
  
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
   * Check if specific folder is writable. 
   * 
   * is_writable() function has problems on Windows because it does not really 
   * checks for ACLs; it checks just the value of Read-Only property and that 
   * is incorect on some Windows installations.
   * 
   * This function will actually try to create (and delete) a test file in order
   * to check if folder is really writable
   *
   * @param string $path
   * @return boolean
   */
  function folder_is_writable($path) {
    if(!is_dir($path)) {
      return false;
    } // if
    
    do {
      $test_file = with_slash($path) . sha1(uniqid(rand(), true));
    } while(is_file($test_file));
    
    $put = @file_put_contents($test_file, 'test');
    if($put === false) {
      return false;
    } // if
    
    @unlink($test_file);
    return true;
  } // folder_is_writable
  
  /**
   * Check if specific file is writable
   * 
   * This function will try to open target file for writing (just open it!) in order to
   * make sure that this file is really writable. There are some known problems with
   * is_writable() on Windows (see description of folder_is_writable() function for more 
   * details).
   * 
   * @see folder_is_writable() function
   * @param string $path
   * @return boolean
   */
  function file_is_writable($path) {
    if(!is_file($path)) {
      return false;
    } // if
    
    $open = @fopen($path, 'a+');
    if($open === false) {
      return false;
    } // if
    
    @fclose($open);
    return true;
  } // file_is_writable
  
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
    
    for($i = 0; $i < $length; $i++) {
      $result .= substr($allowed_chars, rand(0, $allowed_chars_len), 1);
    } // for
    
    return $result;
  } // make_string
  
  /**
   * Check if selected email has valid email format
   *
   * @param string $user_email Email address
   * @return boolean
   */
  function is_valid_email($user_email) {
    if(strstr($user_email, '@') && strstr($user_email, '.')) {
    	return (boolean) preg_match("/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i", $user_email);
    } // if
    return false;
  } // end func is_valid_email
  
  // file_put_contents PHP4 implementaiton, if missing
  if(!function_exists('file_put_contents')) {
    if(!defined('FILE_APPEND')) {
      define('FILE_APPEND', 1);
    } // if
    
    /**
     * File put contents implementeation...
     * 
     * @param string $n
     * @param mixed $d
     * @param mixed $flag
     * @return boolean
     */
    function file_put_contents($n, $d, $flag = false) {
        $mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
        $f = @fopen($n, $mode);
        if($f === false) {
          return 0;
        } else {
          if(is_array($d)) {
            $d = implode($d);
          } // if
          $bytes_written = fwrite($f, $d);
          fclose($f);
          return $bytes_written;
        } // if
    } // file_put_contents
  } // if
  
  if (!function_exists('file_get_contents')) {
    define('PHP_COMPAT_FILE_GET_CONTENTS_MAX_REDIRECTS', 5);
  
    /**
     * Replace file_get_contents()
     *
     * @category    PHP
     * @package     PHP_Compat
     * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
     * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
     * @link        http://php.net/function.file_get_contents
     * @author      Aidan Lister <aidan@php.net>
     * @author      Arpad Ray <arpad@php.net>
     * @version     $Revision: 1.24 $
     * @internal    resource_context is only supported for PHP 4.3.0+ (stream_context_get_options)
     * @since       PHP 5
     * @require     PHP 4.0.0 (user_error)
     */
    function file_get_contents($filename, $incpath = false, $resource_context = null)
    {
        if (is_resource($resource_context) && function_exists('stream_context_get_options')) {
            $opts = stream_context_get_options($resource_context);
        }
        
        $colon_pos = strpos($filename, '://');
        $wrapper = $colon_pos === false ? 'file' : substr($filename, 0, $colon_pos);
        $opts = (empty($opts) || empty($opts[$wrapper])) ? array() : $opts[$wrapper];
    
        switch ($wrapper) {
        case 'http':
            $max_redirects = (isset($opts[$wrapper]['max_redirects'])
                ? $opts[$proto]['max_redirects']
                : PHP_COMPAT_FILE_GET_CONTENTS_MAX_REDIRECTS);
            for ($i = 0; $i < $max_redirects; $i++) {
                $contents = php_compat_http_get_contents_helper($filename, $opts);
                if (is_array($contents)) {
                    // redirected
                    $filename = rtrim($contents[1]);
                    $contents = '';
                    continue;
                }
                return $contents;
            }
            user_error('redirect limit exceeded', E_USER_WARNING);
            return;
        case 'ftp':
        case 'https':
        case 'ftps':
        case 'socket':
            // tbc               
        }
    
        if (false === $fh = fopen($filename, 'rb', $incpath)) {
            user_error('failed to open stream: No such file or directory',
                E_USER_WARNING);
            return false;
        }
    
        clearstatcache();
        if ($fsize = @filesize($filename)) {
            $data = fread($fh, $fsize);
        } else {
            $data = '';
            while (!feof($fh)) {
                $data .= fread($fh, 8192);
            }
        }
    
        fclose($fh);
        return $data;
    }
    
    /**
     * Performs HTTP requests
     *
     * @param string $filename
     *  the full path to request
     * @param array $opts
     *  an array of stream context options
     * @return mixed
     *  either the contents of the requested path (as a string),
     *  or an array where $array[1] is the path redirected to.
     */
    function php_compat_http_get_contents_helper($filename, $opts)
    {
        $path = parse_url($filename);
        if (!isset($path['host'])) {
            return '';
        }
        $fp = fsockopen($path['host'], 80, $errno, $errstr, 4);
        if (!$fp) {
            return '';
        }
        if (!isset($path['path'])) {
            $path['path'] = '/';
        }
        
        $headers = array(
            'Host'      => $path['host'],
            'Conection' => 'close'
        );
        
        // enforce some options (proxy isn't supported) 
        $opts_defaults = array(
            'method'            => 'GET',
            'header'            => null,
            'user_agent'        => ini_get('user_agent'),
            'content'           => null,
            'request_fulluri'   => false
        );
            
        foreach ($opts_defaults as $key => $value) {
            if (!isset($opts[$key])) {
                $opts[$key] = $value;
            }
        }
        $opts['path'] = $opts['request_fulluri'] ? $filename : $path['path'];
        
        // build request
        $request = $opts['method'] . ' ' . $opts['path'] . " HTTP/1.0\r\n";
    
        // build headers
        if (isset($opts['header'])) {
            $optheaders = explode("\r\n", $opts['header']);
            for ($i = count($optheaders); $i--;) {
                $sep_pos = strpos($optheaders[$i], ': ');
                $headers[substr($optheaders[$i], 0, $sep_pos)] = substr($optheaders[$i], $sep_pos + 2);
            }
        }
        foreach ($headers as $key => $value) {
            $request .= "$key: $value\r\n";
        }
        $request .= "\r\n" . $opts['content'];
        
        // make request
        fputs($fp, $request);
        $response = '';
        while (!feof($fp)) {
            $response .= fgets($fp, 8192);
        }
        fclose($fp);    
        $content_pos = strpos($response, "\r\n\r\n");
    
        
        // recurse for redirects
        if (preg_match('/^Location: (.*)$/mi', $response, $matches)) {
            return $matches;
        }
        return ($content_pos != -1 ?  substr($response, $content_pos + 4) : $response);
    }
    
    function php_compat_ftp_get_contents_helper($filename, $opts)
    {
    }
  }
  
  // ---------------------------------------------------
  //  Validators
  // ---------------------------------------------------
  
  /**
   * Test result object
   */
  class TestResult {
  
    /**
     * Result message
     *
     * @var string
     */
    var $message;
    
    /**
     * Result status (error, warning, success etc)
     *
     * @var string
     */
    var $status;
    
    /**
     * New test result
     *
     * @param string $message
     * @param string $status
     * @return TestResult
     */
    function TestResult($message, $status = STATUS_OK) {
      $this->message = $message;
      $this->status = $status;
    }
    
  } // TestResult
  
  /**
   * Validate PHP version
   *
   * @param array $results
   * @return null
   */
  function validate_php(&$results) {
    if(version_compare(PHP_VERSION, '4.3.9') == -1) {
      $results[] = new TestResult('Minimal PHP version required in order to run activeCollab is PHP 4.4. Your PHP version: ' . PHP_VERSION, STATUS_ERROR);
    } elseif(version_compare(PHP_VERSION, '5.1') == -1) {
      $results[] = new TestResult('Your PHP version is ' . PHP_VERSION . '. Recommended version is PHP 5.1 or later', STATUS_WARNING);
    } else {
      $results[] = new TestResult('Your PHP version is ' . PHP_VERSION, STATUS_OK);
    }
  } // validate_php
  
  /**
   * Check if activeCollab is installed
   *
   * @param array $results
   * @return null
   */
  function validate_is_installed(&$results) {
    if(file_exists(INSTALLATION_PATH . '/config/config.php')) {
      $results[] = new TestResult('activeCollab is already installed.', STATUS_ERROR);
    } // if
  } // validate_is_installed
  
  /**
   * Check if $folders are writable
   *
   * @param array $results
   * @return null
   */
  function validate_is_writable(&$results){
    $folders = array(
      'cache',
      'cache/templates',
      'compile',
      'config',
      'logs',
      'public/avatars',
      'public/logos',
      'public/projects_icons',
      'thumbnails',
      'upload',
      'work',
    ); // array
    
  	if(is_array($folders)) {
      foreach($folders as $relative_folder_path) {
        $check_this = INSTALLATION_PATH . '/' . $relative_folder_path;
        
        $is_writable = false;
        if(is_dir($check_this)) {
          $is_writable = folder_is_writable($check_this);
        } elseif(is_file($check_this)) {
          $is_writable = file_is_writable($check_this);
        } // if
        
        if($is_writable) {
          $results[] = new TestResult("/$relative_folder_path is writable", STATUS_OK);
        } else {
          $results[] = new TestResult("/$relative_folder_path is not writable", STATUS_ERROR);
        } // if
      } // foreach
    } // if
  } // validate_is_writeable
  
  /**
   * Check if we have all the required and recommended extensions
   *
   * @param array $results
   * @return null
   */
  function validate_extensions(&$results) {
    $required_extensions = array('mysql', 'pcre', 'tokenizer', 'ctype');
    
    foreach($required_extensions as $required_extension) {
      if(extension_loaded($required_extension)) {
        $results[] = new TestResult("Required extension '$required_extension' found", STATUS_OK);
      } else {
        $results[] = new TestResult("Extension '$required_extension' is required in order to run activeCollab", STATUS_ERROR);
      } // if
    } // foreach
    
    $recommended_extensions = array(
      'gd'       => 'image manipulation', 
      'mbstring' => 'Unicode operations', 
      'iconv'    => 'characterset operations',
      'imap'     => 'connect to POP3/IMAP mailboxes and read email messages',
      'xml'      => 'XML file parsing',
    );
    
    foreach($recommended_extensions as $recommended_extension => $recommended_description) {
      if(extension_loaded($recommended_extension)) {
        $results[] = new TestResult("Recommended extension '$recommended_extension' found", STATUS_OK);
      } else {
        $results[] = new TestResult("Extension '$recommended_extension' was not found. This extension is used for $recommended_description", STATUS_WARNING);
      } // if
    } // foreach
  } // validate_extensions
  
  /**
   * Validate if safe mode is turned on
   *
   * @param array $results
   * @return null
   */
  function validate_safe_mode(&$results) {
    if(ini_get('safe_mode')) {
      $results[] = new TestResult('PHP safe mode is On. It is strongly recommended to turn it Off in your php.ini file', STATUS_WARNING);
    } else {
      $results[] = new TestResult('PHP safe mode is turned Off', STATUS_OK);
    } // if
  } // validate_safe_mode
  
  /**
   * No compatbility mode please
   *
   * @param array $results
   * @return null
   */
  function validate_zend_compatibility_mode(&$results) {
    if(version_compare(PHP_VERSION, '5.0') >= 0) {
      if(ini_get('zend.ze1_compatibility_mode')) {
        $results[] = new TestResult('zend.ze1_compatibility_mode is set to On. This can cause some strange problems. It is strongly recommended to turn this value to Off in your php.ini file', STATUS_WARNING);
      } else {
        $results[] = new TestResult('zend.ze1_compatibility_mode is turned Off', STATUS_OK);
      } // if
    } // if
  } // validate_zend_compatibility_mode
  
  /**
   * Validate database connection
   *
   * @param string $host
   * @param string $user
   * @param string $pass
   * @param string $name
   * @param array $results
   * @return null
   */
  function validate_database_parameteres($host, $user, $pass, $name, &$results) {
    if($connection = mysql_connect($host, $user, $pass)) {
      $results[] = new TestResult('Connected to database as ' . $user . '@' . $host, STATUS_OK);
      
      if(mysql_select_db($name, $connection)) {
        $results[] = new TestResult('Database "' . $name . '" selected', STATUS_OK);
        
        $mysql_version = mysql_get_server_info($connection);
        
        if(version_compare($mysql_version, '4.1') >= 0) {
          $results[] = new TestResult('MySQL version is ' . $mysql_version, STATUS_OK);
          
          $have_inno = false;
          if($result = mysql_query("SHOW VARIABLES LIKE 'have_innodb'", $connection)) {
            if($row = mysql_fetch_assoc($result)) {
              $have_inno = isset($row['Value']) && (strtolower($row['Value']) == 'yes');
            } // if
          } // if
          
          $default_collation = false;
          if($result = mysql_query("SHOW VARIABLES LIKE 'collation_database'", $connection)) {
            if($row = mysql_fetch_assoc($result)) {
              $default_collation = isset($row['Value']) ? $row['Value'] : false;
            } // if
          } // if
          
          if($have_inno) {
            $results[] = new TestResult('InnoDB support is enabled');
          } else {
            $results[] = new TestResult('No InnoDB support. Although activeCollab can use MyISAM storage engine InnoDB is HIGHLY recommended!', STATUS_WARNING);
          }
        } else {
          $results[] = new TestResult('Your MySQL version is ' . $mysql_version . '. We recommend upgrading to at least MySQL 4.1!', STATUS_WARNING);
        } // if
        
      } else {
        $results[] = new TestResult('Failed to select database. MySQL said: ' . mysql_error(), STATUS_ERROR);
      }
    } else {
      $results[] = new TestResult('Failed to connect to database. MySQL said: ' . mysql_error(), STATUS_ERROR);
    } // if
  } // validate_database_parameters
  
?>