<?php

  /**
   * Source module on_object_deleted event handler
   *
   * @package activeCollab.modules.source
   * @subpackage handlers
   */

  /**
   * on_object_deleted handler implemenation
   *
   * @param AngieObject $object
   * @return null
   */
  function source_handle_on_object_deleted($object) {
    if(instance_of($object, 'Ticket') || instance_of($object, 'Discussion') || instance_of($object, 'Milestone') || instance_of($object, 'Task')) {
      db_execute('DELETE FROM ' . TABLE_PREFIX . 'commit_project_objects WHERE object_id = ? AND project_id = ?', $object->getId(), $object->getProjectId());
    } // if
  } // source_handle_on_object_deleted

?>