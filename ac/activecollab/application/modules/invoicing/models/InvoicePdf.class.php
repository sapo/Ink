<?php

  // we need InvoicePdfGenerator
  require_once(INVOICING_MODULE_PATH.'/models/InvoicePdfGenerator.class.php');

  /**
   * Class used for creating PDF invoice reports
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoicePDF {
    /**
     * generate pdf
     *
     * @param Invoice $invoice
     * @return InvoicePdfGenerator
     */
    function preparePDF(&$invoice) {
      $owner_company = get_owner_company();
      $generator = new InvoicePdfGenerator($invoice, $owner_company);
      $generator->paper_format = ConfigOptions::getValue('invoicing_pdf_paper_format');;
      $generator->paper_orientation = ConfigOptions::getValue('invoicing_pdf_paper_orientation');
      $generator->setHeaderFontColor('#'.ConfigOptions::getValue('invoicing_pdf_header_text_color'));
      $generator->setBodyFontColor('#'.ConfigOptions::getValue('invoicing_pdf_page_text_color'));
      $generator->setBorderColor('#'.ConfigOptions::getValue('invoicing_pdf_border_color'));
      $generator->setBackgroundColor('#'.ConfigOptions::getValue('invoicing_pdf_background_color'));
      $generator->FontFamily = 'freesans';      
      return $generator;
    } // preparePDF
    
    /**
     * write invoice PDF to filesystem
     * 
     * @param Invoice $invoice
     * @param string  $filename
     * @return boolean
     */
    function save(&$invoice, $filename) {
      $generator = &InvoicePDF::preparePDF($invoice);
      return $generator->save($filename);
    } // save
    
    /**
     * force PDF download
     *
     * @param Invoice $invoice
     * @param string  $filename
     * @return boolean
     */
    function download(&$invoice, $filename) {
       $generator = &InvoicePDF::preparePDF($invoice);
      return $generator->download($filename);
    } // download
    
    /**
     * Serve PDF inline
     *
     * @param Invoice $invoice
     * @param string  $filename
     * @return boolean
     */
    function inline(&$invoice, $filename) {
      $generator = &InvoicePDF::preparePDF($invoice);
      return $generator->inline($filename);
    } // inline
  } // InvoicePDF

?>