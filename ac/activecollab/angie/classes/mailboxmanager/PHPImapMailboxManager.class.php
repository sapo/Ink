<?php
  // We need MailboxManager
  require_once(MAILBOX_MANAGER_LIB_PATH.'/MailboxManager.class.php');
  
  /**
   * PHPImapMailboxManager
   * 
   * @package angie.classes.mailboxmanager
   */
  class PHPImapMailboxManager extends MailboxManager {    
    /**
     * Tries to connect to server. If it succedes it returns true, if not, it returns error object
     * 
     * @param void
     * @return true
     */
    function connect() {
      $connect_result = parent::connect();
      if (is_error($connect_result)) {
        return $connect_result;
      } // if
      $this->connection = imap_open($this->getConnectionString(), $this->getMailboxUsername(), $this->getMailboxPassword());
      if (!$this->connection) {
        $this->connected = true;
        return new Error(imap_errors());
      } // if
      return $this->connection;
    } // connect
    
    /**
     * Disconnect from pop3/imap server
     *
     * @param void
     * @return boolean
     */
    function disconnect() {
      $this->expunge();
      $result = imap_close($this->connection);
      if ($result) {
        return parent::disconnect();
      } // if
      return false;
    } // disconnect
    
   
    /**
     * Do queued mailbox tasks (delete messages etc...)
     * 
     * @param void
     * @return void
     */
    function expunge() {
      return imap_expunge($this->getConnection());
    } // expunge
    
    /**
     * Return number of messages in mailbox
     *
     * @return integer
     */
    function countMessages() {
      parent::countMessages();  
      return (int) imap_num_msg($this->getConnection());
    } // countMessages
    
    /**
     * Count unread messages in mailbox
     *
     * @return integer
     */
    function countUnreadMessages() {
      parent::countUnreadMessages();
      return (int) imap_num_recent($this->getConnection());
    } // countUnreadMessages
    
    /**
     * Retrieves message headers
     *
     * @param integer $message_id
     * @return array
     */
    function getMessageHeaders($message_id) {
      parent::getMessageHeaders($message_id);
      $header = imap_headerinfo($this->getConnection(), $message_id);
      return $header;
    } // getMessageHeaders()
       
    /**
     * Delete message from server
     * 
     * @param integer $message_id - unique message id
     * @param boolean $instantly - if true, message is removed instantly, and if false, message is removed on disconnection or expunge method
     * @return boolean
     */
    function deleteMessage($message_id, $instantly = false) {
      parent::deleteMessage($message_id);
      // empty error stack
      imap_errors();
      
      $delete = imap_delete($this->getConnection(), $message_id);
      if ($delete) {
        if ($instantly) {
          $this->expunge();
        } // if
        return true;
      } // if
      return new Error(imap_errors());
    } // deleteMessage
    
    /**
     * List messages in mailbox
     *
     * @param integer $start
     * @param integer $count
     */
    function listMessagesHeaders($start, $count) {
      $end = $start + $count - 1;
      $headers = imap_fetch_overview($this->getConnection(),"$start:$end", 0);
      if (count($headers) > 0) {
        for ($x = 0; $x < count($headers); $x++) {
          $headers[$x]->subject = imap_utf8_alt($headers[$x]->subject);
          $headers[$x]->from = imap_utf8_alt($headers[$x]->from);
        } // for
      } // if
      return $headers;
    } // listMessagesHeaders

    
    
/*******************************************************************************  
*   DOWNLOADING AND PARSING MESSAGE
*******************************************************************************/
    /**
     * Downloads message
     *
     * @param integer $message_id
     * @return MailboxManagerEmail
     */
    function getMessage($message_id, $attachments_folder = null) {
      $email = new MailboxManagerEmail();
      $result = $this->parseMessageHeaders($message_id, $email);
      if (is_error($result)) {
        return $result;        
      } // if
      
      $result = $this->parseMessageBody($message_id, $email, $attachments_folder);
      if (is_error($result)) {
        return $result;
      } // if
      
      return $email;
    } // getMessage
    
    /**
     * Returns string representation of main content type
     *
     * @param integer $main_type_id
     * @return string
     */
    function getMainContentType($main_type_id) {
      switch ($main_type_id) {
        case 0: $main_type = 'text'; break;
        case 1: $main_type = 'multipart'; break;
        case 2: $main_type = 'message'; break;
        case 3: $main_type = 'application'; break;
        case 4: $main_type = 'audio'; break;
        case 5: $main_type = 'image'; break;
        case 6: $main_type = 'video'; break;
        case 7: $main_type = 'model'; break;
        default: $main_type = 'x-unknown'; break;
      }
      return $main_type;
    } // getMainContentType
    
    /**
     * Process subcontent type
     *
     * @param string $content_type
     * @return string
     */
    function getSubContentType($content_type) {
      return strtolower($content_type);
    } // getSubContentType
    
    /**
     * Return string representation of provided encoding
     *
     * @param integer $encoding_id
     * @return string
     */
    function getBodyEncodingString($encoding_id) {
      switch ($encoding_id) {
      	case '0': $encoding = '7bit'; break;
      	case '1': $encoding = '8bit'; break;
      	case '2': $encoding = 'binary'; break;
      	case '3': $encoding = 'base64'; break;
      	case '4': $encoding = 'quoted-printable'; break;
      	default: $encoding = 'other'; break;
      } // switch
      return $encoding;
    } // getBodyEncodingString
    
    /**
     * Returns content-type for specified part
     *
     * @param stdClass $part
     * @return string
     */
    function getContentType(& $part) {
      if (!is_object($part)) {
        return false;
      } // if
      
      $type = $this->getMainContentType($part->type);
      if ($part->ifsubtype) {
        $type.='/'.strtolower($part->subtype) ;
      } // if
      
      return $type;
    } // getPartContentType
    
    /**
     * Return specified parameter for part
     *
     * @param stdObject $part
     * @param string $parameter_name
     * @return string
     */
    function getPartParameter(&$part, $parameter_name) {
      $parameter_name = strtoupper($parameter_name);
      if ($part->ifparameters && is_foreachable($part->parameters)) {
        foreach ($part->parameters as $parameter) {
        	if ($parameter_name == strtoupper($parameter->attribute)) {
        	  return $parameter->value;
        	} // if
        } // foreach
      } // if
      return false;
    } // getPartParameter
    
    /**
     * Return specified disposition parameter
     *
     * @param stdObject $part
     * @param string $parameter_name
     * @return string
     */
    function getDispositionParameter(&$part, $parameter_name) {
      $parameter_name = strtoupper($parameter_name);
      if ($part->ifdparameters && is_foreachable($part->dparameters)) {
        foreach ($part->dparameters as $parameter) {
        	if ($parameter_name == strtoupper($parameter->attribute)) {
        	  return $parameter->value;
        	} // if
        } // foreach
      } // if
      return false;
    } // getDispositionParameter
    
    
    /**
     * Parse message headers
     *
     * @param integer $message_id
     * @param MailboxManagerEmail $email
     * @return MailboxManagerEmail
     */
    function parseMessageHeaders($message_id, &$email) {
      if (!instance_of($email, 'MailboxManagerEmail')) {
        $email = new MailboxManagerEmail();
      } // if
      
      $headers = $this->getMessageHeaders($message_id);
      if (!is_object($headers)) {
        return new Error(lang('Could not retrieve headers for that messsage. Does message with id #:message_id exists?', array('message_id' => $message_id)));
      } // if
      
      $email->setId($message_id);
      $email->setSubject(imap_utf8_alt($headers->Subject));
      $email->setDate($headers->Date);
      $email->setSize($headers->Size);
      $email->setHeaders(imap_fetchheader($this->getConnection(), $message_id));
            
      if (is_foreachable($headers->from)) {
        foreach ($headers->from as $from) {
          $email->addAddress($from->mailbox.'@'.$from->host, imap_utf8_alt($from->personal), 'from');
        } // foreach
      } // if
      
      if (is_foreachable($headers->to)) {
        foreach ($headers->to as $to) {
          $email->addAddress($to->mailbox.'@'.$to->host, imap_utf8_alt($to->personal), 'to');
        } // foreach
      } // if
      
      if (is_foreachable($headers->reply_to)) {
        foreach ($headers->reply_to as $reply_to) {
          $email->addAddress($reply_to->mailbox.'@'.$reply_to->host, imap_utf8_alt($reply_to->personal), 'reply_to');
        } // foreach
      } // if
      
      if (is_foreachable($headers->cc)) {
        foreach ($headers->cc as $cc) {
          $email->addAddress($cc->mailbox.'@'.$cc->host, imap_utf8_alt($cc->personal), 'cc');
        } // foreach
      } // if
      
      if (is_foreachable($headers->bcc)) {
        foreach ($headers->bcc as $bcc) {
          $email->addAddress($bcc->mailbox.'@'.$bcc->host, imap_utf8_alt($bcc->personal), 'bcc');
        } // foreach
      } // if
      
      return $email;
    } // parseMessageHeaders
    
    /**
     * Parse message body
     * 
     * @param integer $message_id
     * @param MailboxManagerEmail $email
     * @param string $attachments_folder
     * @return MailboxManagerEmail
     */
    function parseMessageBody($message_id, &$email, $attachments_folder = null) {     
      $structure = imap_fetchstructure($this->getConnection(), $message_id);
      if (!$structure) {
        return new Error(lang('Cannot fetch body structure'));
      } // if
      
      if (!instance_of($email, 'MailboxManagerEmail')) {
        $email = new MailboxManagerEmail();
      } // if
      
      if (isset($structure->parts) && is_foreachable($structure->parts)) {
        $this->parseMessageBodyPart($message_id, $structure, $email, $attachments_folder, null);
      } else {
        $this->parseMessageSinglepartBody($message_id, $structure, $email);
      } // if
      
      return $email;
    } //parseMessageBody
    
    /**
     * parse message body part
     *
     * @param integer $message_id
     * @param stdObject $structure
     * @param MailboxManagerEmail $results
     * @param string $attachments_folder
     * @param string $part_id
     * @return array
     */
    function parseMessageBodyPart($message_id, &$structure, & $results, $attachments_folder, $part_id) {
      $content_type = $this->getContentType($structure);
  		$type = $this->getMainContentType($structure->type);
  		$sub_type = $this->getSubContentType($structure->subtype);
  		$charset = $this->getPartParameter($structure, 'charset');
  		$encoding = $this->getBodyEncodingString($structure->encoding);

      $disposition = 'inline';
      if ($structure->ifdisposition) {
        $disposition = strtolower($structure->disposition);
      } // if
      
      $is_attachment = $this->getPartParameter($structure, 'name') ? true : false;
      
      $part_analyzed = array();
      $part_analyzed['disposition'] = $disposition;
      $part_analyzed['type'] = $type;
      $part_analyzed['content_type'] = $content_type;
      $part_analyzed['sub_type'] = $sub_type;
      $part_analyzed['is_file'] = $is_attachment;
      $part_analyzed['part_id'] = $part_id;
      $part_analyzed['encoding'] = $encoding;
      if ($is_attachment) {
        $part_analyzed['file_name'] = imap_utf8_alt($this->getPartParameter($structure, 'name'));
      } // if
      
      $charset = $this->getPartParameter($structure, 'charset');
      if ($charset) {
        $part_analyzed['charset'] = $charset;
      } // if
      
      switch ($type) {
        case 'multipart':
          $subparts = array();
          for ($x=0; $x < count($structure->parts); $x++) {
            if (!$part_id) {
              $new_part_id = (string) ($x + 1);
            } else {
              $new_part_id = (string) $part_id . '.' . ($x+1);
            } // if
            $subparts[$x] = $this->parseMessageBodyPart($message_id, $structure->parts[$x], $results, $attachments_folder, $new_part_id);
          } // for

          switch ($sub_type) {
            // multipart/alternative
            case 'alternative':
              $counter = count($subparts) - 1;
              if ($subparts[$counter]['type'] != 'multipart') {
                $body_part = array_var($subparts[$counter], 'part_id');
                $charset = strtoupper(array_var($subparts[$counter], 'charset'));
                $encoding = array_var($subparts[$counter], 'encoding');
                $content = $this->getBodyPart($message_id, $body_part, $encoding);
                $results->addBody(
                  $body_part,
                  array_var($subparts[$counter], 'content_type'),
                  $charset == 'UTF-8' ?  $content : convert_to_utf8($content, $charset)
                );
              } // if
              
              $alternative_to = $body_part;
              
              for(--$counter ; $counter >=0 ; --$counter) {
                $body_part = array_var($subparts[$counter], 'part_id');
                $charset = strtoupper(array_var($subparts[$counter], 'charset'));
                $encoding = array_var($subparts[$counter], 'encoding');
                $content = $this->getBodyPart($message_id, $body_part, $encoding);
                $results->addAlternative(
                  $body_part,
                  $alternative_to,
                  array_var($subparts[$counter], 'content_type'),
                  $charset == 'UTF-8' ?  $content : convert_to_utf8($content, $charset)
                );
              } // for
              break;
            // multipart/mixed
            case 'mixed':
  						for ($counter = 0; $counter < count($subparts); ++$counter) {
  						  if ($subparts[$counter]['type'] != 'multipart') {
    						  if ($subparts[$counter]['is_file']) {
    						    $body_part = array_var($subparts[$counter], 'part_id');
    						    $encoding = array_var($subparts[$counter], 'encoding');

    						    do {
    						      $path = $attachments_folder . '/'.make_string(40);
    						    } while (is_file($path));
    						    
    						    $attachment_result = $this->getBodyPart($message_id, $body_part, $encoding, $path);    						    
    						    if ($attachment_result) {
      						    $results->addAttachment(
      						      $body_part,
      						      array_var($subparts[$counter], 'content_type'),
      						      array_var($subparts[$counter], 'file_name'),
                        $path,
                        filesize($path)
      						    );
    						    } // if
    						  } else{
                    $body_part = array_var($subparts[$counter], 'part_id');
                    $charset = strtoupper(array_var($subparts[$counter], 'charset'));
                    $encoding = array_var($subparts[$counter], 'encoding');
                    $content = $this->getBodyPart($message_id, $body_part, $encoding);
                    $results->addBody(
                      $body_part,
                      array_var($subparts[$counter], 'content_type'),
                      $charset == 'UTF-8' ?  $content : convert_to_utf8($content, $charset)
                    );
    						  } // if
  						  } // if
  						} // for
              break;
            // multipart/related
            case 'related':
  						for ($counter = 0; $counter < count($subparts); ++$counter) {
  						  if ($subparts[$counter]['type'] != 'multipart') {
    						  if ($subparts[$counter]['is_file']) {
    						    $body_part = array_var($subparts[$counter], 'part_id');
    						    $encoding = array_var($subparts[$counter], 'encoding');

    						    do {
    						      $path = $attachments_folder . '/'.make_string(40);
    						    } while (is_file($path));
    						    
    						    $attachment_result = $this->getBodyPart($message_id, $body_part, $encoding, $path);
    						    if ($attachment_result) {
      						    $results->addAttachment(
      						      $body_part,
      						      array_var($subparts[$counter], 'content_type'),
      						      array_var($subparts[$counter], 'file_name'),
                        $path,
                        filesize($path)
      						    );
    						    } // if
    						  } else{
                    $body_part = array_var($subparts[$counter], 'part_id');
                    $charset = strtoupper(array_var($subparts[$counter], 'charset'));
                    $encoding = array_var($subparts[$counter], 'encoding');
                    $content = $this->getBodyPart($message_id, $body_part, $encoding);
                    $results->addBody(
                      $body_part,
                      array_var($subparts[$counter], 'content_type'),
                      $charset == 'UTF-8' ?  $content : convert_to_utf8($content, $charset)
                    );
    						  } // if
  						  } // if
  						} // for
              break;
          } // switch
          break;
          
        default:

          break;
      } // switch
      return $part_analyzed;
    } // parseMessageBodyPart;
    
    /**
     * Parse singlepart message
     *
     * @param integer $message_id
     * @param stdObj $structure
     * @param MailboxManagerEmail $email
     * @return MailboxManagerEmail
     */
    function parseMessageSinglepartBody($message_id, &$structure, &$email) {
      $content_type = $this->getContentType($structure);
  		$type = $this->getMainContentType($structure->type);
  		$sub_type = $this->getSubContentType($structure->subtype);
  		$charset = strtoupper($this->getPartParameter($structure, 'charset'));
  		$encoding = $this->getBodyEncodingString($structure->encoding);
  		
  		$content = imap_body($this->getConnection(), $message_id);
      switch ($encoding) {
        case 'base64': 
          $content = imap_base64($content);
          break;
          
        case 'quoted-printable':
          $content = imap_qprint($content);
          break;
      } // switch
      $content = $charset == 'UTF-8' ? $content : convert_to_utf8($content,$charset);
  		$email->addBody(
    		'0',
    		$content_type,
  		  $content
  		);
  		return $email;
    } // parseMessageSinglepartBody
    
    /**
     * Retrieve body part, and if it's file write it to filesystem
     *
     * @param integer $message_id
     * @param string $body_part
     * @param string $file_path
     * @return mixed
     */
    function getBodyPart($message_id, $body_part, $encoding, $file_path = false) {
      if ($file_path) {        
        
        if (!MM_CAN_DOWNLOAD_LARGE_ATTACHMENTS) {
          $structure = imap_bodystruct($this->getConnection(), $message_id, $body_part);
          if (!instance_of($structure, 'stdClass')) {
            return false;
          } // if
          // if attachment is larger than FAIL_SAFE_IMAP_ATTACHMENT_SIZE_MAX, don't download it
          if ($structure->bytes > FAIL_SAFE_IMAP_ATTACHMENT_SIZE_MAX) {
            return false;
          } // if
        } // if
        
        $savebody_result = imap_savebody_alt($this->getConnection(), $file_path, $message_id, $body_part);
        if (!$savebody_result) {
          return false;
        } // if
        
        $temporary_file = $file_path.'_temp';
        switch ($encoding) {
          case 'base64':
            $decoding_result = base64_decode_file($file_path, $temporary_file);
            if ($decoding_result) {
              @unlink($file_path);
              rename($temporary_file, $file_path);
              return true;
            } else {
              @unlink($file_path);
              @unlink($temporary_file);
              return false;
            }
            break;
            
          case 'quoted-printable':
            $decoding_result = quoted_printable_decode_file($file_path, $temporary_file);
            if ($decoding_result) {
              @unlink($file_path);
              rename($temporary_file, $file_path);
              return true;
            } else {
              @unlink($file_path);
              @unlink($temporary_file);
              return false;
            }
            break;
        } // switch
        
        return true;
      } else {
        $result = imap_fetchbody($this->getConnection(), $message_id, $body_part);
        switch ($encoding) {
          case 'base64': 
            return imap_base64($result);
            break;
            
          case 'quoted-printable':
            return imap_qprint($result);
            break;
            
          default:
            return $result;
            break;
        } // switch
      } // if
    } // getBodyPart
    
  } // PHPImapMailboxManager
?>