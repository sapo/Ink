<?php

  /**
   * Incoming mail module on_object_deleted event handler
   *
   * @package activeCollab.modules.incoming_mail
   * @subpackage handlers
   */

  /**
   * on_object_deleted handler implemenation
   *
   * @param AngieObject $object
   * @return null
   */
  function incoming_mail_handle_on_object_deleted($object) {
    if(instance_of($object, 'Project')) {
      db_execute('UPDATE ' . TABLE_PREFIX . 'incoming_mailboxes SET enabled=0  WHERE project_id = ?', $object->getId());
    } // if
  } // incoming_mail_handle_on_object_deleted

?>