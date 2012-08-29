<?php
  /**
   * Init Incoming module
   *
   * @package activeCollab.modules.incoming_mail
   */

  define('INCOMING_MAIL_MODULE', 'incoming_mail');
  define('INCOMING_MAIL_MODULE_PATH', APPLICATION_PATH . '/modules/incoming_mail');

  if (!defined('EMAIL_SPLITTER')) {
    define('EMAIL_SPLITTER', '-- REPLY ABOVE THIS LINE --');
  } // if
  
  define('OBJECT_SOURCE_EMAIL', 'email');

  define('INCOMING_MAIL_ATTACHMENTS_FOLDER',WORK_PATH);

  define('INCOMING_MAIL_DEFAULT_MAILBOX', 'INBOX');

  define('INCOMING_MAIL_OBJECT_TYPE_TICKET', 'ticket');
  define('INCOMING_MAIL_OBJECT_TYPE_DISCUSSION', 'discussion');
  define('INCOMING_MAIL_OBJECT_TYPE_COMMENT', 'comment');

  define('INCOMING_MAIL_STATUS_OK', 0);
  define('INCOMING_MAIL_STATUS_ANONYMOUS_NOT_ALLOWED', 1);
  define('INCOMING_MAIL_STATUS_USER_CANNOT_CREATE_OBJECT', 2);
  define('INCOMING_MAIL_STATUS_PARENT_NOT_EXISTS', 3);
  define('INCOMING_MAIL_STATUS_USER_CANNOT_CREATE_COMMENT', 4);
  define('INCOMING_MAIL_STATUS_SYSTEM_CANNOT_CREATE_OBJECT', 5);
  define('INCOMING_MAIL_STATUS_PROJECT_DOES_NOT_EXISTS', 6);
  define('INCOMING_MAIL_STATUS_UNKNOWN_SENDER', 7);
  define('INCOMING_MAIL_STATUS_PARENT_NOT_ACCEPTING_COMMENTS', 8);

  define('INCOMING_MAIL_LOG_STATUS_OK', 1);
  define('INCOMING_MAIL_LOG_STATUS_ERROR', 0);

  define('INCOMING_MAIL_DEBUG', false);

  use_model('incoming_mailboxes', INCOMING_MAIL_MODULE);
  use_model('incoming_mails', INCOMING_MAIL_MODULE);
  use_model('incoming_mail_attachments', INCOMING_MAIL_MODULE);
  use_model('incoming_mail_activity_logs', INCOMING_MAIL_MODULE);
  
  /**
   * Returns description for provided incoming mail status code
   *
   * @param integer $status_code
   * @return string
   */
  function incoming_mail_module_get_status_description($status_code) {
    $description = array(
      INCOMING_MAIL_STATUS_OK =>                            lang('Email imported successfully'),
      INCOMING_MAIL_STATUS_ANONYMOUS_NOT_ALLOWED =>         lang('Mailbox does not accept emails from unregistered users'),
      INCOMING_MAIL_STATUS_USER_CANNOT_CREATE_OBJECT =>     lang('User does not have permission to create object in selected project'),
      INCOMING_MAIL_STATUS_PARENT_NOT_EXISTS =>             lang('Requested parent object does not exist'),
      INCOMING_MAIL_STATUS_USER_CANNOT_CREATE_COMMENT =>    lang('User does not have permission to create comment in selected object'),
      INCOMING_MAIL_STATUS_SYSTEM_CANNOT_CREATE_OBJECT =>   lang('Object cannot be saved, possibly because of validation errors'),
      INCOMING_MAIL_STATUS_PROJECT_DOES_NOT_EXISTS =>       lang('Project does not exists'),
      INCOMING_MAIL_STATUS_UNKNOWN_SENDER =>                lang('Sender is unknown'),
      INCOMING_MAIL_STATUS_PARENT_NOT_ACCEPTING_COMMENTS => lang('Object does not accept comments. Either it is locked for comments or it does not support comments'),
    );

    if (!array_key_exists($status_code, $description)) {
      return lang('Unkown status code');
    }

    return $description[$status_code];
  } // incoming_mail_module_get_status_description
  
  /**
   * Extract body from MailboxManagerEmail and modify it so we can use it in activecollab
   *
   * @param MailboxManagerEmail $email
   * @return string
   */
  function incoming_mail_get_body(&$email) {
    $body = $email->getBody('text/plain');
    
    if ($body) {
      // if we have plain email to start with
      $body_lines = explode("\n", $body);
      incoming_mail_convert_plain_text_quotes_to_blockquotes($body_lines);
      incoming_mail_convert_plain_signature_to_blockquote($body_lines);
      incoming_mail_convert_reply_to_blockquote($body_lines);
      $body = implode("\n", $body_lines);
    } else {
      // if we have html email to start with
      $body = $email->getBody('text/html');
      if (!trim($body)) {
        return false;
      } // if
      $body = html_to_plain_email($body);
      $body_lines = explode("\n", $body);
      incoming_mail_convert_plain_signature_to_blockquote($body_lines);
      incoming_mail_convert_reply_to_blockquote($body_lines);
      $body = implode("<br />", $body_lines);
    } // if
    
    return trim($body);
  } // incoming_mail_get_body
  
  /**
   * converts plaintext quotes to blockquotes
   *
   * @param string $body_lines
   * @return null
   */
  function incoming_mail_convert_plain_text_quotes_to_blockquotes(&$body_lines) {    
    $block_quote_opened = false;
    $lines = array();
    for ($x = 0; $x < count($body_lines); $x++) {
    	if ((substr_utf($body_lines[$x],0,1) == '>') || (substr_utf($body_lines[$x],0,4) == '&gt;')) {
    	  if (!$block_quote_opened) {
    	    $lines[] = "<blockquote>\n";
    	    $block_quote_opened = true; 
    	  } // if
    	} else {
    	  if ($block_quote_opened) {
    	    $lines[] = "</blockquote>\n";
          $block_quote_opened = false;
    	  } // if
    	} // if
    	$lines[] = clean($body_lines[$x]);
    } // foreach    
	  if ($block_quote_opened) {
	    $lines[] = "</blockquote>";
	  } // if
	  $body_lines = $lines;
  } // incoming_mail_convert_plain_text_quotes_to_blockquotes
  
  /**
   * Convert email reply to blockquote
   *
   * @param array $body_lines
   */
  function incoming_mail_convert_plain_signature_to_blockquote(&$body_lines) {
    $signature_started = false;
    $lines = array();
    for ($x = 0; $x < count($body_lines); $x++) {
    	if (!$signature_started && (($body_lines[$x] == "--\n") || ($body_lines[$x] == "--\r") || ($body_lines[$x] == "--\r\n") || ($body_lines[$x] == "--\n\r"))) {
  	    $lines[] = "<blockquote>\n";
  	    $signature_started = true; 
    	} // if
    	$lines[] = $body_lines[$x];
    } // foreach    
	  if ($signature_started) {
	    $lines[] = "</blockquote>";
	  } // if
	  $body_lines = $lines;
  } // incoming_mail_convert_plain_signature_to_blockquote
  
  /**
   * Convert email reply to blockquote
   *
   * @param array $body_lines
   */
  function incoming_mail_convert_reply_to_blockquote(&$body_lines) {
    $reply_found = false;
    $lines = array();
    $splitters = ConfigOptions::getValue('email_splitter_translations');
    $splitters[] = EMAIL_SPLITTER;
    $splitters[] = '-------- Original Message --------';
    $splitters = array_values($splitters);
    if (is_foreachable($splitters)) {
      for ($x = 0; $x < count($splitters); $x++) {
        $splitters[$x] = trim($splitters[$x]);
        if (!$splitters[$x]) {
          unset($splitters[$x]);
        } // if
      } // for
    } // if

    for ($x = 0; $x < count($body_lines); $x++) {
    	if (!$reply_found && in_array(trim($body_lines[$x]), $splitters)) {
  	    $lines[] = "<blockquote>\n";
  	    $reply_found = true; 
    	} // if
    	$lines[] = $body_lines[$x];
    } // foreach    
	  if ($reply_found) {
	    $lines[] = "</blockquote>";
	  } // if
	  $body_lines = $lines;
  } // incoming_mail_convert_plain_signature_to_blockquote
?>