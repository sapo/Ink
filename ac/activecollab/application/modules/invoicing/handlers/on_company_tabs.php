<?php

  /**
   * on_company_tabs even handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Invoicing module on_company_tabs even handler implementation
   *
   * @param NamedList $tabs
   * @param User $logged_user
   * @param Company $company
   * @return null
   */
  function invoicing_handle_on_company_tabs(&$tabs, &$logged_user, &$company) {
    $tabs->add('invoices', array(
      'text' => lang('Invoices'),
      'url' => assemble_url('people_company_invoices', array('company_id' => $company->getId())),
    ));
  } // invoicing_handle_on_company_tabs

?>