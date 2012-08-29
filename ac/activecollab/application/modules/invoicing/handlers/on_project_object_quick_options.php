<?php

  /**
   * Invoicing module on_project_object_quick_options event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   * @param NamedList $options
   * @param ProjectObject $object
   * @param User $user
   * @return null
   */
  function invoicing_handle_on_project_object_quick_options(&$options, $object, $user) {
    
    /**
     * Add a quick option to create invoice from the ticket
     */
    if(instance_of($object, 'Ticket') && $object->canView($user) && $user->getSystemPermission('can_manage_invoices')) {
      $options->add('make_invoice', array(
        'text' => lang('Create Invoice'),
        'url' => assemble_url('invoices_add', array('ticket_id' => $object->getId())),
      ));     
    } // if
    
  } // invoicing_handle_on_project_object_quick_options

?>