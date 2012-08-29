<?php

  /**
   * Invoicing module on_object_deleted event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * on_object_deleted handler implemenation
   *
   * @param AngieObject $object
   * @return null
   */
  function invoicing_handle_on_object_deleted($object) {
    if(instance_of($object, 'TimeRecord')) {
      db_execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_time_records WHERE time_record_id = ?', $object->getId());
    } // if
  } // invoicing_handle_on_object_deleted

?>