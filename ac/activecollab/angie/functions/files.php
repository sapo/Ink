<?php

  /**
   * General set of functions for file handling
   *
   * @package angie.functions
   */

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
   * Return the files a from specific directory
   * 
   * This function will walk through $dir and read all file names. Result can be filtered by file extension (accepted 
   * param is single extension or array of extensions). If $recursive is set to true this function will walk recursivlly 
   * through subfolders.
   *
   * Example:
   * <pre>
   * $files = get_files($dir, array('doc', 'pdf', 'xst'));
   * foreach($files as $file_path) {
   *   print $file_path;
   * } // if
   * </pre>
   *
   * @param string $dir
   * @param mixed $extension
   * @param boolean $recursive
   * @return array
   */
  function get_files($dir, $extension = null, $recursive = false) {
    if(!is_dir($dir)) {
      return false;
    } // if
    
  	$dir = with_slash($dir);
  	if(!is_null($extension)) {
  	  if(is_array($extension)) {
  	    foreach($extension as $k => $v) {
  	      $extension[$k] = strtolower($v);
  	    } // foreach
  	  } else {
  	    $extension = strtolower($extension);
  	  } // if
  	} // if
  	
		$d = dir($dir);
		$files = array();
		
		while(($entry = $d->read()) !== false) {
		  if(str_starts_with($entry, '.')) {
		    continue;
		  } // if
		  
	    $path = $dir . $entry;
	    
	    if(is_file($path)) {
	    	if(is_null($extension)) {
	    	  $files[] = $path;
	    	} else {
	    		if(is_array($extension)) {
	    		  if(in_array(strtolower(get_file_extension($path)), $extension)) {
	    		    $files[] = $path;
	    		  } // if
	    		} else {
	    		  if(strtolower(get_file_extension($path)) == $extension) {
	    		    $files[] = $path;
	    		  } // if
	    		} // if
	    	} // if
	    } elseif(is_dir($path)) {
	      if($recursive) {
	        $subfolder_files = get_files($path, $extension, true);
	        if(is_array($subfolder_files)) {
	          $files = array_merge($files, $subfolder_files);
	        } // if
	      } // if
	    } // if
		  
		} // while
		
		$d->close();
		return count($files) > 0 ? $files : null;
  } // get_files
  
  /**
   * Return the folder list in provided directory
   * folders are returned with absolute path
   * 
   * @param string $dir
   * @param boolean $recursive
   * @return array
   */
  function get_folders($dir, $recursive = false) {
    if(!is_dir($dir)) {
      return false;
    } // if
  	
    $folders = array();
    
  	if($dirstream = @opendir($dir)) {
  		while(false !== ($filename = readdir($dirstream))) {
 				$path = with_slash($dir) . $filename;
  			if(($filename != '.') && ($filename != '..') && is_dir($path)) {
  			  $folders[] = $path;
  			  if ($recursive) {
    			  $sub_folders = get_folders($path, $recursive);
            if (is_array($sub_folders)) {
              $folders = array_merge($folders, $sub_folders);
            } // if
  			  } // if
  			} // if
  		} // while
  	} // if
  	
  	closedir($dirstream);
  	return $folders;
  }
  	
  
  /**
   * Return file extension from specific filename. Examples:
   * 
   * get_file_extension('index.php') -> returns 'php'
   * get_file_extension('index.php', true) -> returns '.php'
   * get_file_extension('Blog.class.php', true) -> returns '.php'
   *
   * @param string $path File path
   * @param boolean $leading_dot Include leading dot
   * @return string
   */
  function get_file_extension($path, $leading_dot = false) {
  	$filename = basename($path);
  	$dot_offset = (boolean) $leading_dot ? 0 : 1;
  	
    if( ($pos = strrpos($filename, '.')) !== false ) {
      return substr($filename, $pos + $dot_offset, strlen($filename));
    } // if
    
    return '';
  } // get_file_extension
  
	if (!function_exists('mime_content_type')) {
	  /**
	   * Get file mime type in PHP5
	   *
	   * @param path $filename
	   * @return string
	   */
    function mime_content_type($filename) {
      // Sanity check
      if (!file_exists($filename)) {
          return false;
      }

      $filename = escapeshellarg($filename);
      $out = `file -iL $filename 2>/dev/null`;
      if (empty($out)) {
          return 'application/octet-stream';
      }
      // Strip off filename
      $t = substr($out, strpos($out, ':') + 2);
      if (strpos($t, ';') !== false) {
        // Strip MIME parameters
        $t = substr($t, 0, strpos($t, ';'));
      }
      // Strip any remaining whitespace
      return trim($t);
    } // mime_content_type
  }
  
  /**
   * Walks recursively through directory and calculates its total size - returned in bytes
   *
   * @param string $dir Directory
   * @param boolean $skip_files_starting_with_dot (Hidden files)
   * @return integer
   */
  function dir_size($dir, $skip_files_starting_with_dot=true) {
  	$totalsize = 0;
  	
  	if($dirstream = @opendir($dir)) {
  		while(false !== ($filename = readdir($dirstream))) {
  		  if ($skip_files_starting_with_dot) {
    			if(($filename != '.') && ($filename != '..') && ($filename[0]!='.')) {
    				$path = with_slash($dir) . $filename;
    				if (is_file($path)) $totalsize += filesize($path);
    				if (is_dir($path)) $totalsize += dir_size($path, $skip_files_starting_with_dot);
    			} // if
  		  } else {
    			if(($filename != '.') && ($filename != '..')) {
    				$path = with_slash($dir) . $filename;
    				if (is_file($path)) $totalsize += filesize($path);
    				if (is_dir($path)) $totalsize += dir_size($path, $skip_files_starting_with_dot);
    			} // if
  		  }
  		} // while
  	} // if
  	
  	closedir($dirstream);
  	return $totalsize;
  } // end func dir_size
  
  /**
   * Create a new directory
   * 
   * This function will try to create a directory in $path. If $make_writable is 
   * set to true it will also try to chmod it so PHP can write files in it
   *
   * @param string $path
   * @param boolean $make_writable
   * @return boolean
   */
  function create_dir($path, $make_writable = false) {
    if(mkdir($path)) {
      if($make_writable) {
        if(!chmod($path, 0777)) {
          return false;
        } // if
      } // if
    } else {
      return false;
    } // if
    
    return true;
  } // create_dir
  
  /**
    * does the same as mkdir function on php5, except it's compatible with php4,
    * so folders are created recursive
    *
    * @param string $path
    * @param integer $mode
    * @return boolean
    */
  function recursive_mkdir($path, $mode = 0777, $restriction_path = '/') {
    if (DIRECTORY_SEPARATOR == '/') {
      if (strpos($path,$restriction_path) !== 0) {
        return false;
      } // if
    } else {
      if (strpos(strtolower($path), strtolower($restriction_path)) !== 0) {
        return false;
      } // if
    } // if

    $start_path = substr($path,0,strlen($restriction_path));
    $allowed_path = substr($path, strlen($restriction_path));
    $original_path = $path;
    $path = fix_slashes($allowed_path);
    $dirs = explode('/' , $path);
    $count = count($dirs);
    $path = '';
    for ($i = 0; $i < $count; ++$i) {
      if ($i == 0) {
        $path = $start_path;
      } // if
      if (DIRECTORY_SEPARATOR == '\\' && $path=='') {
        $path .= $dirs[$i];
      } else {
        $path .= '/' . $dirs[$i];
      } // if
      if (!is_dir($path) && !mkdir($path, $mode)) {
        return false;
      } // if
    } // if
    
    return is_dir($original_path);
  } // recursive_mkdir
  
  /**
   * Recursive remove directory
   *
   * @param string $folder
   * @param string $restriction_path
   * @return boolean
   */
  function recursive_rmdir($folder, $restriction_path = '/') {
    if (DIRECTORY_SEPARATOR == '/') {
      if (strpos($folder,$restriction_path) !== 0) {
        return false;
      } // if
    } else {
      if (strpos(strtolower($folder), strtolower($restriction_path)) !== 0) {
        return false;
      } // if
    } // if
    
    if (is_dir($folder)) {
      $paths = array_merge(get_files($folder), get_folders($folder));
      foreach($paths as $path) {
        if (is_dir($path) && !is_link($path)) {
          recursive_rmdir($path, $restriction_path);
        } else {
          unlink($path);
        } // if
      }  // foreach
      return rmdir($folder);
    } // if
    return true;
  } // recursive_mkdir
    
  /**
    * Delete $dir only if $base_dir is parent of $dir
    *
    * @param string $dir
    * @param string $base_dir
    * @return boolean
    */
  function safe_delete_dir($dir, $base_dir) {
    if (strpos($dir, $base_dir) === 0) {
      return delete_dir($dir);
    }
    return false;
  } // safe_delete_dir
  
  /**
   * Remove specific directory
   * 
   * This function will walk recursivly through $dir and its subdirectories and delete all content
   *
   * @param string $dir Directory path
   * @return boolean
   */
  function delete_dir($dir) {
    if(!is_dir($dir)) {
      return false;
    } // if
    
  	$dh = opendir($dir);
  	while($file = readdir($dh)) {
  		if(($file != ".") && ($file != "..")) {
  			$fullpath = $dir . "/" . $file;
  			
  			if(!is_dir($fullpath)) {
  				unlink($fullpath);
  			} else {
  				delete_dir($fullpath);
  			} // if
  		} // if
  	} // while

  	closedir($dh);
  	return rmdir($dir) ? true : false;
  } // end func delete_dir
  
  /**
    * Copy folder tree and returns a true if all tree is copied and false if there was errors
    *
    * @param string $source_dir Source Directory
    * @param string $destination_dir Destination Directory
    * @return boolean
    */
  function copy_dir($source_dir, $destination_dir) {
    if(!is_dir($source_dir) || !is_dir($destination_dir)) {
      return false;
    } // if

    $result = true;
       
  	$dh = opendir($source_dir);
  	while($file = readdir($dh)) {
  		if(($file != ".") && ($file != "..") && ($file != '.svn')) {
  			$full_src_path = $source_dir . "/" . $file;
  			$dest_src_path = $destination_dir . "/" . $file;
  			
  			if(!is_dir($full_src_path)) {
  			  $result = $result && (copy($full_src_path, $dest_src_path));
  			} else {
  			  recursive_mkdir($dest_src_path, 0777, WORK_PATH);
  			  $result = $result && (copy_dir($full_src_path, $dest_src_path));
  			} // if
  		} // if
  	} // while
  	closedir($dh);
  	return $result;
  } // copy_dir
  
  /**
   * This function will return true if $dir_path is empty
   * 
   * If $ignore_hidden is set to true any file or folder which name starts with . will be ignored 
   *
   * @param string $dir_path
   * @param boolean $ignore_hidden
   * @return boolean
   */
  function is_dir_empty($dir_path, $ignore_hidden = false) {
    if(!is_dir($dir_path)) {
      return false;
    } // if
    
		$d = dir($dir_path);
    if($d) {
  		while(false !== ($entry = $d->read())) {
  		  if(($entry == '.') || ($entry == '..')) {
  		    continue;
  		  } // if
  		  if($ignore_hidden && ($entry{0} == '.')) {
  		    continue;
  		  } // if
  		  return false;
  		} // while
		} // if
		return true;
  } // is_dir_empty
  
  /**
   * Return path relative to a given path
   *
   * @param string $path
   * @param $relative_to
   * @return string
   */
  function get_path_relative_to($path, $relative_to) {
    return substr($path, strlen($relative_to));
  } // get_path_relative_to
  
  
  if (!function_exists('file_put_contents')) {
    
    if (!defined('FILE_USE_INCLUDE_PATH')) {
        define('FILE_USE_INCLUDE_PATH', 1);
    }
    
    if (!defined('LOCK_EX')) {
        define('LOCK_EX', 2);
    }
    
    if (!defined('FILE_APPEND')) {
        define('FILE_APPEND', 8);
    }
  
  
    /**
      * Replace file_put_contents()
      *
      * @category    PHP
      * @package     PHP_Compat
      * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
      * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
      * @link        http://php.net/function.file_put_contents
      * @author      Aidan Lister <aidan@php.net>
      * @version     $Revision: 1.27 $
      * @internal    resource_context is not supported
      * @since       PHP 5
      * @require     PHP 4.0.0 (user_error)
      */
    function file_put_contents($filename, $content, $flags = null, $resource_context = null) {
        // If $content is an array, convert it to a string
        if (is_array($content)) {
            $content = implode('', $content);
        }
    
        // If we don't have a string, throw an error
        if (!is_scalar($content)) {
            user_error('file_put_contents() The 2nd parameter should be either a string or an array',
                E_USER_WARNING);
            return false;
        }
    
        // Get the length of data to write
        $length = strlen($content);
    
        // Check what mode we are using
        $mode = ($flags & FILE_APPEND) ?
                    'a' :
                    'wb';
    
        // Check if we're using the include path
        $use_inc_path = ($flags & FILE_USE_INCLUDE_PATH) ?
                    true :
                    false;
    
        // Open the file for writing
        if (($fh = @fopen($filename, $mode, $use_inc_path)) === false) {
            user_error('file_put_contents() failed to open stream: Permission denied',
                E_USER_WARNING);
            return false;
        }
    
        // Attempt to get an exclusive lock
        $use_lock = ($flags & LOCK_EX) ? true : false ;
        if ($use_lock === true) {
            if (!flock($fh, LOCK_EX)) {
                return false;
            }
        }
    
        // Write to the file
        $bytes = 0;
        if (($bytes = @fwrite($fh, $content)) === false) {
            $errormsg = sprintf('file_put_contents() Failed to write %d bytes to %s',
                            $length,
                            $filename);
            user_error($errormsg, E_USER_WARNING);
            return false;
        }
    
        // Close the handle
        @fclose($fh);
    
        // Check all the data was written
        if ($bytes != $length) {
            $errormsg = sprintf('file_put_contents() Only %d of %d bytes written, possibly out of free disk space.',
                            $bytes,
                            $length);
            user_error($errormsg, E_USER_WARNING);
            return false;
        }
    
        // Return length
        return $bytes;
    }
  
  }

  /**
   * Format filesize
   *
   * @param string $value
   * @return string
   */
  function format_file_size($value) {
    $data = array(
      'TB' => 1099511627776,
      'GB' => 1073741824,
      'MB' => 1048576,
      'kb' => 1024,
    );
    
    $value = (integer) $value;
    foreach($data as $unit => $bytes) {
      $in_unit = $value / $bytes;
      if($in_unit > 0.9) {
        return number_format($in_unit, 2, NUMBER_FORMAT_DEC_SEPARATOR, NUMBER_FORMAT_THOUSANDS_SEPARATOR) . $unit;
      } // if
    } // foreach
    
    return $value . 'b';
  } // filesize

  if(!function_exists('file_get_contents')) {
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
    function file_get_contents($filename, $incpath = false, $resource_context = null) {
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
    function php_compat_http_get_contents_helper($filename, $opts) {
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
    
    function php_compat_ftp_get_contents_helper($filename, $opts) {
    }
  }

  if(!function_exists('sys_get_temp_dir')) {
    
    /**
      * Returns folder for temporary files
      *
      * @return string
      */
    function sys_get_temp_dir() {
      if (!empty($_ENV['TMP'])) {
        return realpath( $_ENV['TMP']);
      } else if (!empty($_ENV['TMPDIR'])) {
        return realpath( $_ENV['TMPDIR']);
      } else if (!empty($_ENV['TEMP'])) {
        return realpath($_ENV['TEMP']);
      } else {
        $temp_file = tempnam(md5(uniqid(rand(), TRUE)),'');
        if ($temp_file) {
          $temp_dir = realpath(dirname($temp_file));
          unlink($temp_file);
          return $temp_dir;
        } // if
      } // if
      return false;
    } // sys_get_temp_dir
    
  } // if

  /**
   * Decode base64 encoded file
   *
   * @param string $input_file
   * @param string $output_file
   * @return boolean
   */
  function base64_decode_file($input_file, $output_file) {
    $input_handle = fopen($input_file, 'r');
    if (!$input_handle) {
      return false;
    } // if
    $output_handle = fopen($output_file, 'w');
    if (!$output_handle) {
      return false;
    } // if
    
    while (!feof($input_handle)) {
    	$encoded = fgets($input_handle);
    	$decoded = base64_decode($encoded);
    	fwrite($output_handle, $decoded);
    } // while;
    
    if (fclose($input_handle) && fclose($output_handle)) {
      return true;
    }
    return false;
  } // base64_decode_file

  /**
   * Decode quoted_printable encoded file
   *
   * @param string $input_file
   * @param string $output_file
   * @return boolean
   */
  function quoted_printable_decode_file($input_file, $output_file) {
    $input_handle = fopen($input_file, 'r');
    if (!$input_handle) {
      return false;
    } // if
    $output_handle = fopen($output_file, 'w');
    if (!$output_handle) {
      return false;
    } // if
    
    while (!feof($input_handle)) {
    	$encoded = fgets($input_handle);
    	$breaklines = 0;
    	if (substr($encoded,-1) == "\n" || substr($encoded, -1) == "\r") {
    	  $breaklines ++;
    	} // if
    	if (substr($encoded,-2, 1) == "\n" || substr($encoded, -2, 1) == "\r") {
    	  $breaklines ++;
    	} // if
    	if (substr($encoded, -3 - $breaklines, 3) == '=0D') {
    	  $encoded = (substr($encoded, 0, -3 - $breaklines) . substr($encoded, - $breaklines));
    	} // if
    	$decoded = quoted_printable_decode($encoded);
    	fwrite($output_handle, $decoded);
    } // while;
    
    if (fclose($input_handle) && fclose($output_handle)) {
      return true;
    } // if
    return false;
  } // quoted_printable_decode_file
  
  /**
   * Check if file source can be displayed
   *
   * @param string $filename
   * @return boolean
   */
  function file_source_can_be_displayed($filename) {
    return in_array(get_file_extension($filename), get_displayable_file_types());
  } // file_source_can_be_displayed
  
  /**
   * Get file extensions for files whose source makes sense when printed 
   *
   * @param null
   * @return array
   */
  function get_displayable_file_types() {
    return array(  
    'ada',
    'adb',
    'adp',
    'ads',
    'ans',
    'as',
    'asc',
    'asm',
    'asp',
    'aspx',
    'atom',
    'au3',
    'bas',
    'bat',
    'bmax',
    'bml',
    'c',
    'cbl',
    'cc',
    'cfm',
    'cgi',
    'cls',
    'cmd',
    'cob',
    'cpp',
    'cs',
    'css',
    'csv',
    'cxx',
    'd',
    'dif',
    'dtd',
    'e',
    'efs',
    'egg',
    'egt',
    'f',
    'f77',
    'for',
    'frm',
    'frx',
    'ftn',
    'ged',
    'gm6',
    'gmd',
    'gml',
    'h',
    'hpp',
    'hs',
    'hta',
    'htaccess',
    'htm',
    'html',
    'hxx',
    'ici',
    'ictl',
    'ihtml',
    'inc',
    'inf',
    'ini',
    'java',
    'js',
    'jsfl',
    'l',
    'las',
    'lasso',
    'lassoapp',
    'lua',
    'm',
    'm4',
    'makefile',
    'manifest',
    'met',
    'metalink',
    'ml',
    'mrc',
    'n',
    'ncf',
    'nfo',
    'nut',
    'p',
    'pas',
    'php',
    'php3',
    'php4',
    'php5',
    'phps',
    'phtml',
    'piv',
    'pl',
    'pm',
    'pp',
    'properties',
    'ps1',
    'ps1xml',
    'psc1',
    'psd1',
    'psm1',
    'py',
    'pyc',
    'pyi',
    'rb',
    'rdf',
    'resx',
    'rss',
    's',
    'scm',
    'scpt',
    'sh',
    'shtml',
    'spin',
    'stk',
    'svg',
    'tab',
    'tcl',
    'tpl',
    'txt',
    'vb',
    'vbp',
    'vbs',
    'xht',
    'xhtml',
    'xml',
    'xsl',
    'xslt',
    'xul',
    'y',
    );
  } // get_displayable_file_types
  
  
  /**
   * Creates attachment from uploaded file
   *
   * @param array $file
   * @param ApplicationObject $parent
   * @return Attachment
   */
  function &make_attachment($file, $parent = null) {
    if (!isset($file) || !isset($file['tmp_name'])) {
      return new Error(lang('File is not uploaded'));
    } // if
    
    $destination_file = get_available_uploads_filename();
    
    if (!move_uploaded_file($file['tmp_name'], $destination_file)) {
      return new Error(lang('Could not move uploaded file to uploads directory'));
    } // if
    
    $attachment = new Attachment();
    $attachment->setName($file['name']);
    $attachment->setLocation(basename($destination_file));
    $attachment->setMimeType(array_var($file,'type','application/octet-stream'));
    $attachment->setSize(array_var($file, 'size', 0));
    
    if (instance_of($parent, 'ApplicationObject')) {
      $attachment->setParent($parent);
    } // if
    
    $save = $attachment->save();
    if (!$save || is_error($save)) {
      @unlink($destination_file);
      return $save;
    } // if
    return $attachment; 
  } // make_attachment
  
?>