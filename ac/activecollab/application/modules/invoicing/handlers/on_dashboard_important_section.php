<?php

  /**
   * Invoicing on_dashboard_important_section handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Handle on_dashboard_important_section event
   *
   * @param NamedList $items
   * @param User $user
   * @return null
   */
  function invoicing_handle_on_dashboard_important_section(&$items, &$user) {
    $company = $user->getCompany();
    // if user can manage invoices, list overdue invoices for all companies
    if ($user->getSystemPermission('can_manage_invoices')) {
      // if it's administrator list only overdue invoices
      if (($admin_overdue_invoices = Invoices::countOverdue()) > 0) {
        $items->add('admin_overdue_invoices', array(
          'label'       => $admin_overdue_invoices > 1 ? lang('<strong>:count</strong> overdue invoices for all companies', array('count' => $admin_overdue_invoices)) : lang('<strong>:count</strong> overdue invoice for all companies', array('count' => $admin_overdue_invoices)),
          'class'       => 'adminoverdue_invoices',
          'icon'        => get_image_url('important.gif'),
          'url'         => assemble_url('invoices'),
        ));
      } // if
    } // if
    
    // if user is company manager or can manage invoices show outstanding and overdue invoices for his company
    if ($user->isCompanyManager($company) || $user->getSystemPermission('can_manage_invoices')) {
      //
      // Outstanding Invoices
      //
      $issued_invoices_count = Invoices::countOutstanding($company);
      if ($issued_invoices_count > 0) {
        if ($issued_invoices_count == 1) {
          // if there is only one outstanding invoice, then link should open that very same invoice
          $issued_invoices = Invoices::findOutstanding($company, array(INVOICE_STATUS_ISSUED));
          $link_url = $issued_invoices[0]->getCompanyViewUrl();
          $label = lang('<strong>1</strong> outstanding invoice for your company');
        } else {
          // if there is multuple outstanding invoices, then link should open company invoices pages
          $link_url = assemble_url('people_company_invoices', array('company_id' => $company->getId()));
          $label = lang('<strong>:count</strong> outstanding invoices for your company', array('count' => $issued_invoices_count));
        } // if
        $items->add('issued_invoices', array(
          'label'       => $label,
          'class'       => 'issued_invoices',
          'icon'        => get_image_url('icon_small.gif', INVOICING_MODULE),
          'url'         => $link_url,
        ));
      } // if
      
      //
      // Overdue Invoices
      //
      $overdue_invoices_count = Invoices::countOverdue($company);
      if ($overdue_invoices_count > 0) {
        if ($overdue_invoices_count == 1) {
          // if there is only one overdue invoice, then link should open that very same invoice
          $overdue_invoices = Invoices::findOverdue($company, array(INVOICE_STATUS_ISSUED));
          $link_url = $overdue_invoices[0]->getCompanyViewUrl();
          $label = lang('<strong>1</strong> overdue invoice for your company');
        } else {
          // if there is multuple overdue invoices, then link should open company invoices pages
          $link_url = assemble_url('people_company_invoices', array('company_id' => $company->getId()));
          $label = lang('<strong>:count</strong> overdue invoices for your company', array('count' => $overdue_invoices));
        } // if
        
        $items->add('overdue_invoices', array(
          'label'       => $label,
          'class'       => 'overdue_invoices',
          'icon'        => get_image_url('important.gif'),
          'url'         => $link_url,
        ));
      } // if
    } // if
  } // on_dashboard_important_section
?>