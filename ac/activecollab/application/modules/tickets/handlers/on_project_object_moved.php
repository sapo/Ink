<?php

  /**
   * Tickets handle on_project_object_moved event
   *
   * @package activeCollab.modules.tickets
   * @subpackage handlers
   */
  
  /**
   * Handle on_project_object_moved event
   *
   * @param ProjectObject $object
   * @param Project $source
   * @param Project $destination
   * @return null
   */
  function tickets_handle_on_project_object_moved(&$object, &$source, &$destination) {
    if(instance_of($object, 'Ticket')) {
      $object->setTicketId(Tickets::findNextTicketIdByProject($destination));
      $object->save();
    } // if
  } // tickets_handle_on_project_object_moved

?>