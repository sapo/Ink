<?php

  /**
   * Functions for MailboxManager module
   * 
   * @package angie.classes.mailboxmanager
   */
  
  /**
   * Returns default port for server security type
   *
   * @param string $security
   * @return string
   */
  function mm_get_default_port($security) {
    switch ($security) {
    	case MM_SECURITY_NONE:
    		return '110';
    		break;
    		
    	case MM_SECURITY_TLS:
    	  return '110';
    	  break;
    	  
    	case MM_SECURITY_SSL:
    	  return '993';
    	  break;
    
    	default:
    	  return '110';
    		break;
    } // switch
  } // mm_get_default_port
  
  /**
   * Removes reply from message dependable of client
   *
   * @param string $body
   * @param string $email_client
   * @param string $content_type
   * @param array $splitters
   */
  function mm_remove_reply(&$body, $email_client, $content_type, $splitters) {
    if (!$body || !$email_client) {
      return $body;
    } // if
          
    $positions = array();
    foreach ($splitters as $splitter) {
      $position = strpos($body, $splitter);
      if ($position !== false) {
        $positions[] = $position;
      };
    } // foreach

    if (is_foreachable($positions)) {
      $body = substr($body, 0, min($positions));
    } // if
    
    /**
    switch ($email_client) {
    	case MM_EMAIL_CLIENT_APPLE_MAIL:
    	  if ($content_type == 'text/html') {
          $body = preg_replace('/\<div\>\<div\>(.*?)\<\/div\>\<br\ class\=\"Apple\-interchange\-newline\"\>(.*)/is', '', $body);
          $body = trim(preg_replace('/\<html\>\<body(.*?)\>/is', '', $body));
    	  } else {
    	    if (is_foreachable($positions)) {
      	    $body = explode("\n", $body);
      	    $body = trim(implode("\n", array_slice($body, 0, count($body) - 3)));
    	    } // if
    	  } // if
    		break;
    		
    	case MM_EMAIL_CLIENT_GMAIL:
    	  if ($content_type == 'text/html') {
    	    pre_var_dump($content_type);
    	  } else {
    	    $swift = new Swift_Message();
    	    $swift->
    	  } // if
    	  break;
    
    	default:
    		break;
    } // switch
    pre_var_dump($body);
    
    */
		return $body;
  } // mm_remove_reply
  
  
  /**
   * Alternative imap_utf8 function
   *
   * @param string $something_to_decode
   */
  function imap_utf8_alt($something_to_decode) {
    if (!trim($something_to_decode)) {
      return null;
    } // if
    
    // if function exists we will try to use it in order to decode subject, otherwise we use buggy imap_utf8
    if (function_exists('imap_mime_header_decode')) {
      $decoded = imap_mime_header_decode($something_to_decode);
      if (is_foreachable($decoded)) {
        $decoded_string = '';
        foreach ($decoded as $element) {
          if ((strtoupper($element->charset) != 'UTF-8') && (strtoupper($element->charset) != 'DEFAULT')) {
            $decoded_string.= convert_to_utf8($element->text,$element->charset);
          } else {
            $decoded_string.= $element->text;
          } // if
        } // foreach
        $decoded_string = trim($decoded_string);
        if ($decoded_string) {
          return $decoded_string;
        } // if
      } // if
    } // if
    
    $decoded_string = trim(imap_utf8($something_to_decode));
    if (strlen_utf($decoded_string) > 0) {
      return $decoded_string;
    } // if
    
    return $something_to_decode;
  } // if
  
  if ((FAIL_SAFE_IMAP_FUNCTIONS == true) || (!function_exists('imap_savebody'))) {
    /**
     * we define our own savebody function
     *
     * @param resource $imap_stream
     * @param mixed $file
     * @param int $msg_number
     * @param string $part_number
     * @param int $options
     * @return boolean
     */
    function imap_savebody_alt($imap_stream, $file, $msg_number, $part_number=null, $options=null) {
      $fetch_data = imap_fetchbody($imap_stream, $msg_number, $part_number, $options);
      if ($fetch_data === false) {
        return false;
      } // if
      return file_put_contents($file, $fetch_data);
    } // imap_savebody_alt
    
  } else {
    /**
     * define proxy function for imap_savebody
     *
     * @param resource $imap_stream
     * @param mixed $file
     * @param int $msg_number
     * @param string $part_number
     * @param int $options
     * @return boolean
     */
    function imap_savebody_alt($imap_stream, $file, $msg_number, $part_number=null, $options=null) {
      return imap_savebody($imap_stream, $file, $msg_number, $part_number, $options);
    } // imap_savebody_alt
  } // if
  
?>