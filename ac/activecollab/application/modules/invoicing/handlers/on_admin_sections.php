<?php

  /**
   * Invoicing module on_admin_sections event handler
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  // Define new administration section
  define('ADMIN_SECTION_INVOICING', lang('Invoicing'));

  /**
   * Handle on_admin_sections event
   *
   * @param array $sections
   * @return null
   */
  function invoicing_handle_on_admin_sections(&$sections) {
    $sections[ADMIN_SECTION_INVOICING][INVOICING_MODULE] = array(
      array(
        'name'        => lang('Company Identity'),
        'description' => lang('Change company identity information that will be show in PDF report'),
        'url'         => assemble_url('admin_invoicing_company_identity'),
        'icon'        => get_image_url('admin-identity-settings.gif', INVOICING_MODULE),
      ),
      array(
        'name'        => lang('PDF Settings'),
        'description' => lang('Configure settings for invoicing module'),
        'url'         => assemble_url('admin_invoicing_pdf'),
        'icon'        => get_image_url('admin-pdf-settings.gif', INVOICING_MODULE),
      ),
      array(
        'name'        => lang('Number Generator'),
        'description' => lang('Configure automatic invoice number generator'),
        'url'         => assemble_url('admin_invoicing_number'),
        'icon'        => get_image_url('invoice_number.gif', INVOICING_MODULE)
      ),
      array(
        'name'        => lang('Item Templates'),
        'description' => lang('Configure predefiend invoicing items'),
        'url'         => assemble_url('admin_invoicing_items'),
        'icon'        => get_image_url('admin-items-settings.gif', INVOICING_MODULE),
      ),
      array(
        'name'        => lang('Note Templates'),
        'description' => lang('Configure predefined notes for invoices'),
        'url'         => assemble_url('admin_invoicing_notes'),
        'icon'        => get_image_url('admin-notes-settings.gif', INVOICING_MODULE),
      ),
      array(
        'name'        => lang('Tax Rates'),
        'description' => lang('Configure tax rates for Invoicing module'),
        'url'         => assemble_url('admin_tax_rates'),
        'icon'        => get_image_url('tax-rates.gif', INVOICING_MODULE),
      ),
      array(
        'name'        => lang('Currencies'),
        'description' => lang('Configure currencies and default hourly rates for Invoicing module'),
        'url'         => assemble_url('admin_currencies'),
        'icon'        => get_image_url('currencies.gif', INVOICING_MODULE),
      ),
    );
  } // invoicing_handle_on_admin_sections

?>