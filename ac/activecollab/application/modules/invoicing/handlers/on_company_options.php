<?php

  /**
   * on_company_options event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */
  
  /**
   * Handle on_company_options event
   *
   * @param Company $company
   * @param NamedList $options
   * @param User $logged_user
   * @return null
   */
  function invoicing_handle_on_company_options(&$company, &$options, &$logged_user) {
    if(Invoice::canAccessCompanyInvoices($logged_user, $company)) {
      $options->add('invoices', array(
        'text'    => lang('Invoices'),
        'url'     => assemble_url('people_company_invoices', array('company_id' => $company->getId())),
        'icon'    => get_image_url('company-invoices.gif', INVOICING_MODULE),
      ));
    } // if
  } // invoicing_handle_on_company_options

?>