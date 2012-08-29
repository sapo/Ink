<?php
  /**
   * Class for importing mail from mailboxes
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage models
   *
   */
  class IncomingMailImporter extends AngieObject {
    /**
     * Open's connection to mailboxes and download emails with limit of $max_emails
     *
     * @param array $mailboxes
     * @param integer $max_emails
     */
    function importEmails (&$mailboxes, $max_emails = 20) {
      use_model('incoming_mail_activity_logs', INCOMING_MAIL_MODULE);

      $import_date = new DateTimeValue();
      $imported_emails_count = 0;
      if (is_foreachable($mailboxes)) {
        foreach ($mailboxes as $mailbox) {
        	$manager = $mailbox->getMailboxManager();
        	// open connection to mailbox
        	$result = $manager->connect();
        	if (!$result || is_error($result)) {
        	  // we didn't connect, so we need to log it
        	  $error_message = '';
        	  if (is_error($result)) {
              $error_message = ': '.$result->getMessage();
        	  } // if
        	  IncomingMailActivityLogs::log($mailbox->getId(), lang('Could Not Connect To Mailbox' . $error_message), null, INCOMING_MAIL_LOG_STATUS_ERROR, $import_date);
            $mailbox->setLastStatus(2);
            $mailbox->save();
        	  continue;
        	} // if

          $mailbox->setLastStatus(1);
          $mailbox->save();

        	$email_count = $manager->countMessages();
        	for ($mid = 1; $mid < ($email_count+1); $mid++) {
        	  if ($imported_emails_count >= $max_emails) {
        	    return true;
        	  } // if
            $current_message_id = 1;
        	  
            $email = $manager->getMessage($current_message_id, INCOMING_MAIL_ATTACHMENTS_FOLDER);
            if (!instance_of($email, 'MailboxManagerEmail')) {
              IncomingMailActivityLogs::log($mailbox->getId(), $email->getMessage(), null, INCOMING_MAIL_LOG_STATUS_ERROR, $import_date);
              continue;
            } // if

            $pending_email = IncomingMailImporter::createPendingEmail($email, $mailbox);
            if (!instance_of($pending_email, 'IncomingMail')) {
              IncomingMailActivityLogs::log($mailbox->getId(), $pending_email->getMessage(), $email, INCOMING_MAIL_LOG_STATUS_ERROR, $import_date);
              continue;
            } // if
            $manager->deleteMessage($current_message_id, true);

            $project_object = IncomingMailImporter::importPendingEmail($pending_email);
            if (!instance_of($project_object, 'ProjectObject')) {
              IncomingMailActivityLogs::log($mailbox->getId(), $project_object->getMessage(), $pending_email, INCOMING_MAIL_LOG_STATUS_ERROR, $import_date);
              continue;
            } // if

            IncomingMailActivityLogs::log($mailbox->getId(), lang('Imported Successfully'), $project_object, INCOMING_MAIL_LOG_STATUS_OK, $import_date);
            $user = $project_object->getCreatedBy();
            if (instance_of($user, 'User')) {
              $user->setLastActivityOn(new DateTimeValue());
              $user->save();
            } // if
            $pending_email->delete();
            
            $imported_emails_count ++;
      	  } // for
        } // foreach
      } // if
    } // importEmails

    /**
     * Creates pending incoming email from email message
     *
     * @param MailboxManagerEmail $email
     * @param IncomingMailbox $mailbox
     *
     * @return mixed
     */
    function createPendingEmail(&$email, &$mailbox) {
      if (!instance_of($email, 'MailboxManagerEmail')) {
        return new Error(lang('Email provided is empty'));
      } // if

      $incoming_mail = new IncomingMail();
      $incoming_mail->setProjectId($mailbox->getProjectId());
      $incoming_mail->setIncomingMailboxId($mailbox->getId());
      $incoming_mail->setHeaders($email->getHeaders());

      // object subject
      $subject = $email->getSubject();
      $incoming_mail->setSubject($subject);
      
      // object body
      $incoming_mail->setBody(incoming_mail_get_body($email));

      // object type and parent id
      $object_type = $mailbox->getObjectType();
      preg_match("/\{ID(.*?)\}(.*)/is", $subject, $results);
      if (count($results) > 0) {
        $parent_id = $results[1];
        $parent = ProjectObjects::findById($parent_id);
        if (instance_of($parent, 'ProjectObject') && $parent->can_have_comments) {
          $object_type = 'comment';
          $incoming_mail->setParentId($parent_id);
        } else {
          $incoming_mail->setParentId(null);
        } // if
        $subject = trim(str_replace($results[0],'',$subject));
        $incoming_mail->setSubject($subject);
      } // if
      $incoming_mail->setObjectType($object_type);

      if ($incoming_mail->getSubject() || $incoming_mail->getBody()) {
        if (!$incoming_mail->getSubject()) {
          $incoming_mail->setSubject(lang('[SUBJECT NOT PROVIDED]'));
        } // if
        if (!$incoming_mail->getBody() && (in_array($incoming_mail->getObjectType(), array('comment', 'discussion')))) {
          $incoming_mail->setBody(lang('[CONTENT NOT PROVIDED]'));
        } // if
      } // if

      $sender = $email->getAddress('from');
      if (!is_array($sender)) {
        return new Error(lang('Sender is unknown'));
      } // if

      // user details
      $email_address = array_var($sender, 'email', null);
      $user = Users::findByEmail($email_address);
      if (!instance_of($user,'User')) {
        $user = new AnonymousUser(array_var($sender, 'name', null) ? array_var($sender, 'name', null) : $email_address, $email_address);
      } // if
      $incoming_mail->setCreatedBy($user);

      // creation time
      $incoming_mail->setCreatedOn(new DateTimeValue());

      $result = $incoming_mail->save();
      if (!$result || is_error($result)) {
        return $result;
      } // if

      // create attachment objects
      $attachments = $email->getAttachments();
      if (is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
        	$incoming_attachment = new IncomingMailAttachment();
        	$incoming_attachment->setTemporaryFilename(basename(array_var($attachment, 'path', null)));
        	$incoming_attachment->setOriginalFilename(array_var($attachment,'filename', null));
        	$incoming_attachment->setContentType(array_var($attachment, 'content_type', null));
        	$incoming_attachment->setFileSize(array_var($attachment, 'size', null));
        	$incoming_attachment->setMailId($incoming_mail->getId());
        	$attachment_save = $incoming_attachment->save();
        	if (!$attachment_save || is_error($attachment_save)) {
        	  // we couldn't create object in database so we need to remove file from system
        	  //@unlink(array_var($attachment,'path'));
        	} // if
        } // foreach
      } // if
      return $incoming_mail;
    } // createPendingEmail

    /**
     * Use $incoming_mail as a base for creating ProjectObject
     *
     * @param IncomingMail $incoming_mail
     * @return integer
     */
    function importPendingEmail(&$incoming_mail, $skip_permission_checking = false) {
      $mailbox = IncomingMailboxes::findById($incoming_mail->getIncomingMailboxId());

      $project = $incoming_mail->getProject();
      if (!instance_of($project, 'Project')) {
        // project does not exists
        $incoming_mail->setState(INCOMING_MAIL_STATUS_PROJECT_DOES_NOT_EXISTS);
        $incoming_mail_save = $incoming_mail->save();
        return new Error(incoming_mail_module_get_status_description(INCOMING_MAIL_STATUS_PROJECT_DOES_NOT_EXISTS));
      } // if
      
      $user = $incoming_mail->getCreatedBy();
      if (!$skip_permission_checking) {
        // check additional permissions
        if (instance_of($user, 'User')) { // if it's registered user
          
          // if object type is not comment and all users cannot create objects and current user cant create object
          if (($incoming_mail->getObjectType() != 'comment') && !$mailbox->getAcceptAllRegistered() && !ProjectObject::canAdd($user, $project,$incoming_mail->getObjectType())) {
            $incoming_mail->setState(INCOMING_MAIL_STATUS_USER_CANNOT_CREATE_OBJECT);
            $incoming_mail_save = $incoming_mail->save();
            return new Error(incoming_mail_module_get_status_description(INCOMING_MAIL_STATUS_USER_CANNOT_CREATE_OBJECT));
          } // if
        } else { // if it's anonymous user
          // if mailbox does not accept anonymous users
          if (!$mailbox->getAcceptAnonymous()) {
            $incoming_mail->setState(INCOMING_MAIL_STATUS_ANONYMOUS_NOT_ALLOWED);
            $incoming_mail_save = $incoming_mail->save();
            return new Error(incoming_mail_module_get_status_description(INCOMING_MAIL_STATUS_ANONYMOUS_NOT_ALLOWED));
          } // if
        } // if
      } // if

      // create new object instance dependable of object type
      switch ($incoming_mail->getObjectType()) {
        case 'discussion':
          $import = & IncomingMailImporter::importPendingEmailAsDiscussion($incoming_mail, $project, $user);
          break;

        case 'ticket':
          $import = & IncomingMailImporter::importPendingEmailAsTicket($incoming_mail, $project, $user);
          break;

        case 'comment':
          $import = & IncomingMailImporter::importPendingEmailAsComment($incoming_mail, $project, $user, $mailbox);
          break;
      } // switch

      return $import;
    } // importPendingEmail


    /**
     * Import pending email as ticket
     *
     * @param IncomingMail $incoming_mail
     * @param Project $project
     * @param User $user
     * @return Ticket
     */
    function importPendingEmailAsTicket(&$incoming_mail, &$project, &$user) {
      $ticket = new Ticket();
      $ticket->setProjectId($project->getId());
      $ticket->setCreatedBy($user);
      $ticket->setCreatedOn($incoming_mail->getCreatedOn());
      $ticket->setVisibility(VISIBILITY_NORMAL);
      $ticket->setState(STATE_VISIBLE);
      $ticket->setSource(OBJECT_SOURCE_EMAIL);

      $ticket->setName($incoming_mail->getSubject());
      $ticket->setBody($incoming_mail->getBody());

      IncomingMailImporter::attachFilesToProjectObject($incoming_mail, $ticket);

      $save = $ticket->save();
      if ($save && !is_error($save)) {
        $subscibed_users = array($project->getLeaderId());
        if (instance_of($user, 'User')) {
         $subscibed_users[] = $user->getId();
        } // if
        Subscriptions::subscribeUsers($subscibed_users, $ticket);
        $ticket->ready();
        return $ticket;
      } // if
      return $save;
    } // importPendingEmailAsTicket


    /**
     * Import pending email as discussion
     *
     * @param IncomingMail $incoming_mail
     * @param Project $project
     * @param User $user
     * @return Discussion
     */
    function importPendingEmailAsDiscussion(&$incoming_mail, &$project, &$user) {
      $discussion = new Discussion();
      $discussion->setProjectId($project->getId());
      $discussion->setCreatedBy($user);
      $discussion->setCreatedOn($incoming_mail->getCreatedOn());
      $discussion->setVisibility(VISIBILITY_NORMAL);
      $discussion->setState(STATE_VISIBLE);
      $discussion->setSource(OBJECT_SOURCE_EMAIL);
      
      $discussion->setName($incoming_mail->getSubject());
      $discussion->setBody($incoming_mail->getBody());  
         
      IncomingMailImporter::attachFilesToProjectObject($incoming_mail, $discussion);
      
      $save = $discussion->save();
      if ($save && !is_error($save)) {
        $subscibed_users = array($project->getLeaderId());
        if (instance_of($user, 'User')) {
         $subscibed_users[] = $user->getId();
        } // if
        Subscriptions::subscribeUsers($subscibed_users, $discussion);
        $discussion->ready();
        return $discussion;
      } // if
      return $save;
    } // importPendingEmailAsDiscussion

    
    /**
     * Imports pending email as comment to commentable object
     *
     * @param IncomingMail $incoming_mail
     * @param Project $project
     * @param User $user
     * @param IncomingMailbox $mailbox
     * @return Comment
     */
    function importPendingEmailAsComment(&$incoming_mail, &$project, &$user, &$mailbox) {
      $parent = ProjectObjects::findById($incoming_mail->getParentId());
      if (!instance_of($parent, 'ProjectObject')) {
        // parent object does not exists
        $incoming_mail->setState(INCOMING_MAIL_STATUS_PARENT_NOT_EXISTS);
        $incoming_mail_save = $incoming_mail->save();
        return new Error(incoming_mail_module_get_status_description(INCOMING_MAIL_STATUS_PARENT_NOT_EXISTS));
      } // if
      
      if (!$mailbox->getAcceptAllRegistered() && instance_of($user, 'User') && !$parent->canComment($user)) {
        // user cannot create comments to parent object
        $incoming_mail->setState(INCOMING_MAIL_STATUS_USER_CANNOT_CREATE_COMMENT);
        $incoming_mail_save = $incoming_mail->save();
        return new Error(incoming_mail_module_get_status_description(INCOMING_MAIL_STATUS_USER_CANNOT_CREATE_COMMENT));
      } else {
        if(!$parent->can_have_comments || $parent->getIsLocked() || ($parent->getState() < STATE_VISIBLE))  {
          // parent object can't have comments
          $incoming_mail->setState(INCOMING_MAIL_STATUS_USER_CANNOT_CREATE_COMMENT);
          $incoming_mail_save = $incoming_mail->save();
          return new Error(incoming_mail_module_get_status_description(INCOMING_MAIL_STATUS_USER_CANNOT_CREATE_COMMENT));          
        } // if
      } // if

      $comment = new Comment();
      $comment->log_activities = false;
      $comment->setCreatedBy($user);
      $comment->setCreatedOn($incoming_mail->getCreatedOn());
      $comment->setProjectId($parent->getProjectId());
      $comment->setState(STATE_VISIBLE);
      $comment->setSource(OBJECT_SOURCE_EMAIL);
      $comment->setVisibility($parent->getVisibility());
      $comment->setParent($parent);
      $comment->setBody($incoming_mail->getBody());

      IncomingMailImporter::attachFilesToProjectObject($incoming_mail, $comment);

      $save = $comment->save();
      if ($save && !is_error($save)) {
        $activity = new NewCommentActivityLog();
        $activity->log($comment, $user);
        
        if (instance_of($user, 'User')) {
          $parent->subscribe($user);
        } // if
        $comment->ready();
        return $comment;
      } // if
      return $save;
    } // importPendingEmailAsComment

    /**
     * Attach files from incoming mail to $project_object
     *
     * @param IncomingMail $incoming_mail
     * @param ProjectObject $project_object
     * @return null
     */
    function attachFilesToProjectObject(&$incoming_mail, &$project_object) {
      $attachments = $incoming_mail->getAttachments();
      $formated_attachments = array();
      if (is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
        	$formated_attachments[] = array(
        	 'path' => INCOMING_MAIL_ATTACHMENTS_FOLDER.'/'.$attachment->getTemporaryFilename(),
        	 'filename' => $attachment->getOriginalFilename(),
        	 'type' => strtolower($attachment->getContentType()),
        	);
        } // foreach
        attach_from_array($formated_attachments, $project_object);
      } // if
    } // attachFilesToProjectObject

  } // IncomingMailImporter