<?php

  require_once(ANGIE_PATH .'/classes/tcpdf/init.php');
  
  /**
   * Class used for creating PDF invoice reports
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoicePdfGenerator extends TCPDF {
    
    /**
     * Invoice object
     *
     * @var Invoice
     */
    var $invoice;
    
    /**
     * Owner company
     * 
     * @var Company
     */
    var $owner_company;
      
    /**
     * Items table column widths in percentages
     *
     * @var array
     */
    var $items_table_column_widths;
    
    /**
     * Itams table labels
     * 
     * @var array
     */
    var $items_table_column_labels;
    
    /**
     * Default paper size
     *
     * @var string
     */
    var $paper_format = DEFAULT_PAPER_FORMAT;
    
    /**
     * Default paper orientation
     *
     * @var string
     */
    var $paper_orientation = DEFAULT_PAPER_ORIENTATION;
    
    /**
     * array of colors to be used in drawing
     *
     * @var array
     */
    var $colors;
    
    /**
     * Default font size
     *
     * @var integer
     */
    var $font_size = 9;
    
    /**
     * default line height
     *
     * @var integer
     */
    var $line_height = 4;
    
    /**
     * invoice label
     * 
     * @var string
     */
    var $invoice_label = false;
       
    /**
     * Constructor
     *  $invoice - invoice object
     *  $page_orientation - P (Portrait) or L (Lanscape)
     *  $unit - mm
     *  $page_format - A3, A4, A5, Letter, Legal
     *
     * @param Invoice $invoice
     * @return void
     */
    function __construct($invoice = null, $owner_company = null, $page_orientation = 'P', $unit = 'mm', $page_format = 'A4') {
      parent::__construct($page_orientation, $unit, $page_format);
      if (instance_of($invoice, 'Invoice')) {
        $this->invoice = $invoice;
        $this->invoice_label = $this->invoice->getStatus() == INVOICE_STATUS_DRAFT ? 
          lang('PRO FORMA :num', array('num' => $this->invoice->getName(true)), true, $this->invoice->getLanguage()) : 
          lang('INVOICE :num', array('num' => $this->invoice->getName(true)), true, $this->invoice->getLanguage());
      } // if
      if (instance_of($owner_company, 'Company')) {
        $this->owner_company = $owner_company;
      } // if
       
      // Margins
      $this->SetMargins(15, 10, 15);
  
      $this->items_table_column_labels = array(
        '',
        lang('Description', null, true, $this->invoice->getLanguage()),
        lang('Qty.', null, true, $this->invoice->getLanguage()),
        lang('Unit Cost', null, true, $this->invoice->getLanguage()),
        lang('Tax', null, true, $this->invoice->getLanguage()),
        lang('Total', null, true, $this->invoice->getLanguage()),
      );
      
      $this->items_table_column_widths = array(
        '5',
        '47',
        '12',
        '12',
        '12',
        '12',
      );
      
      $this->setPrintHeader(false);
    } // __construct
    
    /**
     * Sets header font color
     *
     * @param string $color
     */
    function setHeaderFontColor($color) {
      $this->colors['header_font_color'] = $color ? $color : '000000';
    } // color
    
    /**
     * retrieves header font color
     *
     * @return string
     */
    function getHeaderFontColor() {
      return array_var($this->colors, 'header_font_color', '000000');
    } // getHeaderFontColor
    
    /**
     * sets body font color
     * 
     * @param string $color
     */
    function setBodyFontColor($color) {
      $this->colors['body_font_color'] = $color ? $color : '000000';
    } // setBodyFontColor
    
    /**
     * retrieves body font color
     *
     * @return string
     */
    function getBodyFontColor() {
      return array_var($this->colors, 'body_font_color', '000000');
    } // getBodyFontColor
    
    /**
     * sets border color
     * 
     * @param string $color
     */
    function setBorderColor($color) {
      $this->colors['border_color'] = $color ? $color : '999999';
    } // setBorderColor
    
    /**
     * retrieves border color
     *
     * @return string
     */
    function getBorderColor() {
      return array_var($this->colors, 'border_color', '999999');
    } // getBorderColor
    
    /**
     * sets background color
     * 
     * @param string $color
     */
    function setBackgroundColor($color) {
      $this->colors['background_color'] = $color ? $color : 'cccccc';
    } // setBackgroundColor  
    
    /**
     * retrieves background color
     *
     * @return string
     */
    function getBackgroundColor() {
      return array_var($this->colors, 'background_color', 'cccccc');
    } // getBackgroundColor
    
    /**
     * Set invoice
     *
     * @param Invoice $invoice
     * @return void
     */
    function setInvoice($invoice) {
      $this->invoice = $invoice;
      $this->invoice_label = $this->invoice->getStatus() == INVOICE_STATUS_DRAFT ? 
        lang('PRO FORMA :num', array('num' => $this->invoice->getName(true)), true, $this->invoice->getLanguage()) : 
        lang('INVOICE :num', array('num' => $this->invoice->getName(true)), true, $this->invoice->getLanguage());
    } // setInvoice
    
    /**
     * Get Invoice
     *
     * @param void
     * @return invoice
     */
    function getInvoice() {
      return $this->invoice;   
    } // getInvoice
    
    /**
     * Set owner company
     *
     * @param Company $owner_company
     * @return void
     */
    function setOwnerCompany($owner_company) {
      $this->owner_company = $owner_company;
    } // setOwnerCompany
    
    /**
     * Get owner company
     *
     * @param void
     * @return Company
     */
    function getOwnerCompany() {
      return $this->owner_company;
    } // getOwnerCompany
    
    /**
     * Calculate page width
     *
     * @return integer
     */
    function PageWidth(){
        return (int) $this->w-$this->rMargin-$this->lMargin;
    } // PageWidth
    
    /**
     * Calculate page height
     *
     * @return integer
     */
    function PageHeight(){
        return (int) $this->h-$this->tMargin-$this->bMargin;
    } // PageHeight
    
    /**
     * Calculate width of specified column of items table
     *
     * @param integer $column_number
     * @return integer
     */
    function getItemsTableColumnWidth($column_number) {       
      return ($this->PageWidth() * $this->items_table_column_widths[$column_number] / 100);
    } // getItemsTableColumnWidth
    
    /**
     * Draws a horizontal line
     *
     * @param integer $y
     * @param integer $line_width
     * @return null
     */
    function drawHorizontalLine() {
      $starting_x = $this->GetX();
      $starting_y = $this->GetY();
      
      $this->Line($this->lMargin, $starting_y, $this->lMargin + $this->PageWidth(), $starting_y);
    } // drawHorizontalLine
    
    /**
     * Draws empty (white) space on the page
     *
     * @param integer $height
     */
    function drawWhiteSpace($height) {
      $this->SetY($this->GetY()+$height);
    } // drawWhiteSpace
    
    /**
     * Draws invoice header
     * 
     * @param null
     * @return null
     */
    function drawInvoiceHeaderBlock() {
      //  remember starting coordinates      
      $starting_y = $this->GetY();
      $starting_x = $this->GetX();
      
      $company_image = get_company_invoicing_logo_path();
      if (is_file($company_image)) {
        $this->Image($company_image,$this->GetX(),$this->GetY(), null, 20);
      } // if
      $max_y1 = $this->getImageRBY();
      
      $this->SetX($starting_x);
      $this->SetY($starting_y);
      
      // draws company details
      $rgb = $this->convertHTMLColorToDec($this->getHeaderFontColor());
      $this->SetTextColor($rgb['R'],$rgb['G'],$rgb['B']);
      
      $company_name = ConfigOptions::getValue('invoicing_company_name');
      if (!$company_name) {
        $company_name = $this->owner_company->getName();
      } // if
      
      $company_details = ConfigOptions::getValue('invoicing_company_details');
      if (!$company_details) {
        $company_details = '';
        if ($this->owner_company->getConfigValue('office_address')) {
          $company_details .= "\n" . $this->owner_company->getConfigValue('office_address');
        } // if
        if ($this->owner_company->getConfigValue('office_phone')) {
          $company_details .= "\n" . $this->owner_company->getConfigValue('office_phone');
        } // if
        if ($this->owner_company->getConfigValue('office_fax')) {
          $company_details .= "\n" . $this->owner_company->getConfigValue('office_fax');
        } // if
        if ($this->owner_company->getConfigValue('office_homepage')) {
          $company_details .= "\n" . $this->owner_company->getConfigValue('office_homepage');
        } // if
      } // if
      
      $this->SetFont('','B',$this->font_size);
      $this->Cell(0, $this->line_height, $company_name, 0 , 0, 'R');
      $this->Ln();
  
      $this->SetX($x);
      $this->SetFont('','',$this->font_size);
      $this->MultiCell(0, $this->line_height, $company_details, 0, 'R', false, 1, '', '', true, 0, false, 1.25);
      $max_y2 = $this->GetY();
      
      $this->SetX($starting_x);
      $this->SetY(max($max_y1, $max_y2));
    } // drawInvoiceHeaderBlock

    
    /**
     * Draws invoice details
     *
     * @param null
     * @return null
     */
    function drawInvoiceDetailsBlock() {
      //  remember starting coordinates      
      $starting_y = $this->GetY();
      $starting_x = $this->GetX();
      
      $rgb = $this->convertHTMLColorToDec($this->getBodyFontColor());
      $this->SetTextColor($rgb['R'],$rgb['G'],$rgb['B']);
      
      $block_width = $this->getItemsTableColumnWidth(0) + $this->getItemsTableColumnWidth(1);
           
      $this->SetFont('', 'B', $this->font_size + 6);
      $this->Cell($block_width, $this->line_height + 3, $this->invoice_label);
      $this->Ln();
      
      $this->SetFont('', '', $this->font_size);
      $content = '';
      
      require_once SMARTY_PATH . '/plugins/modifier.date.php';
      if ($this->invoice->isIssued() || $this->invoice->isBilled()) {
        if ($this->invoice->getIssuedOn()) {
          $content.= lang('Issued On: :issued_date', array('issued_date' => smarty_modifier_date($this->invoice->getIssuedOn(),0)), true, $this->invoice->getLanguage()) . "\n";
        } // if
        $content.= lang('Due On: :due_date', array('due_date' => smarty_modifier_date($this->invoice->getDueOn(),0)), true, $this->invoice->getLanguage()) . "\n";
      } // if
      
      $this->MultiCell($block_width, $this->line_height, $content, 0, 'L', false, 1, '', '', true, 0, false, 1.5);
      $this->Ln();
      $max_y1 = $this->GetY();
      
      
      $block_width = $this->getItemsTableColumnWidth(3) + $this->getItemsTableColumnWidth(4) + $this->getItemsTableColumnWidth(5);
      
      // draw client info
      $corner_width = 5;      
      $padding = 5;
      $client_info_starting_x = $this->getPageWidth() - $block_width - $this->lMargin;
      
      $this->Line($client_info_starting_x, $starting_y, $client_info_starting_x + $corner_width, $starting_y);
      $this->Line($client_info_starting_x, $starting_y, $client_info_starting_x, $starting_y + $corner_width);
      $this->Line($client_info_starting_x + $block_width - $corner_width, $starting_y, $client_info_starting_x + $block_width, $starting_y);
      $this->Line($client_info_starting_x + $block_width, $starting_y, $client_info_starting_x + $block_width, $starting_y + $corner_width);
      
      $this->SetFont('','B', $this->font_size);
      $this->SetY($starting_y + $padding);
      $company = $this->invoice->getCompany();
      if (instance_of($company, 'Company')) {
        $this->SetX($client_info_starting_x + $padding);
        $this->Cell($block_width - (2 * $padding), $this->line_height, $company->getName());
        $this->Ln();
        $this->SetX($client_info_starting_x + $padding);
      } // if
      $this->SetFont('','', $this->font_size);
      $this->MultiCell($block_width - (2 * $padding), $this->line_height, $this->invoice->getCompanyAddress(), 0, 'L', false, 1, '', '', true, 0, false, 1.25);
      $max_y2 = $this->GetY();
      
      $this->Line($client_info_starting_x, $max_y2 + $padding, $client_info_starting_x + $corner_width, $max_y2 + $padding);
      $this->Line($client_info_starting_x, $max_y2 + $padding, $client_info_starting_x, $max_y2 + $padding - $corner_width);
      $this->Line($client_info_starting_x + $block_width - $corner_width, $max_y2 + $padding, $client_info_starting_x + $block_width, $max_y2 + $padding);
      $this->Line($client_info_starting_x + $block_width, $max_y2 + $padding, $client_info_starting_x + $block_width, $max_y2 + $padding - $corner_width);
      
      $this->SetY(max($max_y1, $max_y2));
    } // drawInvoiceDetails
       
    /**
     * Insert table header onto page
     * 
     * @param null
     * @return null
     */
    function drawItemsTableHeader() {
      $rgb = $this->convertHTMLColorToDec($this->getBodyFontColor());
      $this->SetTextColor($rgb['R'],$rgb['G'],$rgb['B']);
      
      //  remember starting coordinates      
      $starting_y = $this->GetY();
      $starting_x = $this->GetX();
           
      $this->SetFont('','B',$this->font_size);
      if (is_foreachable($this->items_table_column_labels)) {
        $this->Cell($this->getItemsTableColumnWidth(0), $this->line_height+4, $this->items_table_column_labels[0],1,0,'R',true,false);
      	$this->Cell($this->getItemsTableColumnWidth(1), $this->line_height+4, $this->items_table_column_labels[1],1,0,'L',true,false);
      	$this->Cell($this->getItemsTableColumnWidth(2), $this->line_height+4, $this->items_table_column_labels[2],1,0,'R',true,false);
      	$this->Cell($this->getItemsTableColumnWidth(3), $this->line_height+4, $this->items_table_column_labels[3],1,0,'R',true,false);
      	$this->Cell($this->getItemsTableColumnWidth(4), $this->line_height+4, $this->items_table_column_labels[4],1,0,'R',true,false);
      	$this->Cell($this->getItemsTableColumnWidth(5), $this->line_height+4, $this->items_table_column_labels[5],1,0,'R',true,false);
      	$this->Ln();
      } // if
    } // drawItemsTableHeader
     
    /**
     * Draws table rows
     *
     * @param null
     * @return null
     */
    function drawItemsTableRows() {
      $rgb = $this->convertHTMLColorToDec($this->getBodyFontColor());
      $this->SetTextColor($rgb['R'],$rgb['G'],$rgb['B']);
      
      //  remember starting coordinates      
      $starting_y = $this->GetY();
      $starting_x = $this->GetX();
      
      $this->SetFont('', '', $this->font_size);
      if (is_foreachable($this->invoice->getItems())) {
        $row_id = 1;
       foreach ($this->invoice->getItems() as $item ) {
          $height = ceil($this->GetStringWidth($item->getDescription()) / $this->getItemsTableColumnWidth(1)) * $this->line_height;
          if (($this->GetY() + $height) > $this->PageHeight()) {
            $this->AddPage();
          } // if
          $temp_y = $this->GetY();
          $rel_height = $this->GetY();
          $this->SetX($this->getItemsTableColumnWidth(0) + $starting_x);
        	$this->MultiCell($this->getItemsTableColumnWidth(1), $this->line_height, $item->getDescription(),1,'L',false);
        	$rel_height = $this->GetY() - $rel_height;
        	$this->SetY($temp_y);
        	$this->SetX($starting_x);
        	$this->MultiCell($this->getItemsTableColumnWidth(0), $rel_height, $row_id.'.',1,'R',false,false);
        	$this->SetX($this->getItemsTableColumnWidth(0) + $this->getItemsTableColumnWidth(1) + $this->lMargin);
        	$this->MultiCell($this->getItemsTableColumnWidth(2), $rel_height, $item->getQuantity(),1,'R',false,false);
        	$this->MultiCell($this->getItemsTableColumnWidth(3), $rel_height, number_format($item->getUnitCost(), 2, NUMBER_FORMAT_DEC_SEPARATOR, NUMBER_FORMAT_THOUSANDS_SEPARATOR),1,'R',false, false);
        	$this->MultiCell($this->getItemsTableColumnWidth(4), $rel_height, number_format($item->getTax(), 2, NUMBER_FORMAT_DEC_SEPARATOR, NUMBER_FORMAT_THOUSANDS_SEPARATOR),1,'R',false, false);
        	$this->MultiCell($this->getItemsTableColumnWidth(5), $rel_height, number_format($item->getTotal(), 2, NUMBER_FORMAT_DEC_SEPARATOR, NUMBER_FORMAT_THOUSANDS_SEPARATOR),1,'R',false, false);
        	$this->Ln();
        	$row_id ++;
        } // foreach     
      } // if
    } // drawItemsTableRows
    
    /**
     * Draw invoice totals
     *
     * @param null
     * @return unknown
     */
    function drawInvoiceTotals() {
      $rgb = $this->convertHTMLColorToDec($this->getBodyFontColor());
      $this->SetTextColor($rgb['R'],$rgb['G'],$rgb['B']);
      
      //  remember starting coordinates      
      $starting_y = $this->GetY();
      $starting_x = $this->GetX();
       
      $totals_width = (int) ($this->PageWidth() - $totals_width);
      
      $this->SetFont('', '', $this->font_size);
      
      $col1_width = $this->getItemsTableColumnWidth(3);
      $col2_width = $this->getItemsTableColumnWidth(4) + $this->getItemsTableColumnWidth(5);
      
      $starting_x = $this->PageWidth() - $col1_width - $col2_width + $starting_x;
      
      $this->SetX($starting_x);
      $this->Cell($col1_width, $this->line_height, lang('Sub Total:', null, true, $this->invoice->getLanguage()), 'B', 0, 'R', false, false);
      $this->Cell($col2_width, $this->line_height, number_format($this->invoice->getTotal(), 2, NUMBER_FORMAT_DEC_SEPARATOR, NUMBER_FORMAT_THOUSANDS_SEPARATOR), 'B', 0, 'R', false, false);
      $this->Ln();
      $this->SetX($starting_x);
      $this->Cell($col1_width, $this->line_height, lang('Tax:', null, true, $this->invoice->getLanguage()), 'B', 0, 'R', false, false);
      $this->Cell($col2_width, $this->line_height, number_format($this->invoice->getTax(), 2, NUMBER_FORMAT_DEC_SEPARATOR, NUMBER_FORMAT_THOUSANDS_SEPARATOR), 'B', 0, 'R', false, true);
      $this->Ln();$this->Ln();
      $this->SetFont('', 'B', $this->font_size);
      $this->SetX($starting_x);
      $this->Cell($col1_width, $this->line_height, lang('Total:', null, true, $this->invoice->getLanguage()), 'B', 0, 'R', true, false);
      $this->Cell($col2_width, $this->line_height, $this->invoice->getCurrencyCode() . ' ' . number_format($this->invoice->getTaxedTotal(), 2, NUMBER_FORMAT_DEC_SEPARATOR, NUMBER_FORMAT_THOUSANDS_SEPARATOR), 'B', 0, 'R', true, true);
      $this->Ln();
    } // drawInvoiceTotals
    
    
    /**
     * Draw invoice note block
     *
     * @param null
     * @return null
     */
    function drawInvoiceNote() {
      if (!trim($this->invoice->getNote())) {
        return false;
      } // if
      $rgb = $this->convertHTMLColorToDec($this->getBodyFontColor());
      $this->SetTextColor($rgb['R'],$rgb['G'],$rgb['B']);
      
      //  remember starting coordinates      
      $starting_y = $this->GetY();
      $starting_x = $this->GetX();
      
      $this->SetFont('', 'B', $this->font_size);
      $this->Cell(0, $this->line_height, lang('Note:', null, true, $this->invoice->getLanguage()));
      $this->Ln();
      $this->SetFont('', '', $this->font_size);
      $this->MultiCell(0, $this->line_height, $this->invoice->getNote(), 0, 'L', false, 1, '', '', true, 0, false, 1.25);
    } // drawInvoiceNote
    
    /**
     * Prints page number and invoice numer on every page in footer
     * 
     * @param void
     * @return void
     */
    function Footer($line_height = 4) {
      $rgb = $this->convertHTMLColorToDec($this->getHeaderFontColor());
      $this->SetTextColor($rgb['R'],$rgb['G'],$rgb['B']);
      
      $this->SetX($this->lMargin);
      $this->SetY(-15);
      $this->SetFont('', 'B', $this->font_size);
      $this->Cell($this->PageWidth()+5,$line_height,lang('Page :page_no of :total_pages', array('page_no' => $this->PageNo(), 'total_pages' => $this->getAliasNbPages()), true, $this->invoice->getLanguage()), 0, 0, 'R');
      $this->SetX($this->lMargin);
      $this->Cell(0,$line_height, $this->invoice_label, 0, 0, 'L');
    } // Footer
    
    /**
     * Generate invoice
     * 
     * @param void
     * @return void
     */
    function generate() {
      if (!instance_of($this->invoice, 'Invoice')) {
        new Error('Invoice object is not specified', true);
      } // if
      
      $this->SetTitle($this->invoice->getName(), true);
      $this->SetAuthor('activeCollab (http://www.vbsupport.org/forum/)');
      $this->SetAutoPageBreak(true, 20);
      $this->AddPage($this->paper_orientation, $this->paper_format);
      $this->SetFont('', '', 12);
  
      $rgb = $this->convertHTMLColorToDec($this->getBorderColor());
      $this->SetDrawColor($rgb['R'],$rgb['G'],$rgb['B']);
      $rgb = $this->convertHTMLColorToDec($this->getBackgroundColor());
      $this->SetFillColor($rgb['R'],$rgb['G'],$rgb['B']);

      $this->tMargin = 15;
      
      // draw invoice header
      $this->drawInvoiceHeaderBlock();
      
      // draw horizontal line
      $this->drawWhiteSpace(5);
      $this->drawHorizontalLine();
      
      // draw invoice details
      $this->drawWhiteSpace(10);
      $this->drawInvoiceDetailsBlock();
      
      // draw items table
      $this->drawWhiteSpace(20);
      $this->drawItemsTableHeader();
      $this->drawItemsTableRows();
      
      // draw invoice totals
      $this->drawWhiteSpace(10);
      $this->drawInvoiceTotals();
      
      // draw invoice note
      $this->drawWhiteSpace(10);
      $this->drawInvoiceNote();

      $this->AliasNbPages();
    } // generate
    
    /**
     * New page event handler
     * 
     * @param null
     * @return boolean
     */
    function AcceptPageBreak() {
      $this->current_y = 0 + $this->tMargin;
      return parent::AcceptPageBreak();
    } // AcceptPageBreak
        
    /**
     * Serve PDF inline
     *
     * @param string $filename
     * @return boolean
     */
    function inline($filename) {
      $this->generate();
      parent::Output($filename, 'I');
    } // inline
    
    /**
     * Download PDF
     *
     * @param string $name
     * @return boolean
     */
    function download($filename = null) {
      $this->generate();
      parent::Output($filename, 'D');
    } // download
    
    /**
     * Save PDF to filesystem
     *
     * @param string $filename
     * @return boolean
     */
    function save($filename) {
      $this->generate();
      return parent::Output($filename,'F');
    } //save
  } // InvoicePdfGenerator
?>