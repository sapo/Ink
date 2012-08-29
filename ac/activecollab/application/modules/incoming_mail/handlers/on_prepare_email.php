<?php

  /**
   * Incoming mail module on_prepare_email handler
   *
   * @package activeCollab.modules.incoming_mail
   * @subpackage handlers
   */
  
  /**
   * Prepare email message
   *
   * @param string $tpl
   * @param User $recipient
   * @param ProjectObject $context
   * @param string $body
   * @param string $subject
   * @param array $attachments
   * @param Language $language
   * @return null
   */
  function incoming_mail_handle_on_prepare_email($tpl, $recipient_email, $context, &$body, &$subject, &$attachments, &$language) {
    if(instance_of($context, 'ProjectObject') && $context->can_have_comments) {
      $subject .= ' {ID' . $context->getId() . '}';
      if (instance_of($language, 'Language')) {
        $email_splitter = lang(EMAIL_SPLITTER, null, true, $language);
      } else {
        $email_splitter = lang(EMAIL_SPLITTER);
      } // if
      $body = '<p>' . $email_splitter . '</p>' . $body;
    } // if
  } // incoming_mail_handle_on_prepare_email

?>