<?php
  // we need admin controller
  use_controller('admin');
  
  /**
   * Invoice number generator controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoiceNumberGeneratorAdminController extends AdminController {

    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'Invoice_number_generator_admin';
    
    /**
     * Index page
     * 
     * @param void
     * @return null
     */
    function index() {
      $this->wireframe->addBreadCrumb(lang('Invoicing'), assemble_url('admin'));
      $this->wireframe->addBreadCrumb(lang('Number Generator'), assemble_url('admin_invoicing_number'));
      
      // prepare javascript variables and counters for preview
      $pattern = Invoices::getInvoiceNumberGeneratorPattern();
      list($total_counter, $year_counter, $month_counter) = Invoices::getDateInvoiceCounters();
      $total_counter++; $year_counter++; $month_counter++;
      
      $variable_year = date('Y');
      $variable_month = date('n');
      $variable_month_short = date('M');
      $variable_month_long = date('F');
      
      js_assign('pattern_variables', array(
        INVOICE_VARIABLE_CURRENT_YEAR => $variable_year,
        INVOICE_VARIABLE_CURRENT_MONTH => $variable_month,
        INVOICE_VARIABLE_CURRENT_MONTH_SHORT => $variable_month_short,
        INVOICE_VARIABLE_CURRENT_MONTH_LONG => $variable_month_long,
        INVOICE_NUMBER_COUNTER_TOTAL => $total_counter,
        INVOICE_NUMBER_COUNTER_YEAR => $year_counter,
        INVOICE_NUMBER_COUNTER_MONTH => $month_counter
      ));
      
      $generator_data = $this->request->post('generator');
      if (!is_foreachable($generator_data)) {
        $generator_data = array(
          'pattern' => Invoices::getinvoiceNumberGeneratorPattern(),
        );
      } // if
      
      $this->smarty->assign(array(
        'generator_data' => $generator_data
      ));
      
      if ($this->request->isSubmitted()) {
        $errors = new ValidationErrors();
        
        $posted_pattern = array_var($generator_data, 'pattern', null);
        if (!trim($posted_pattern)) {
          $errors->addError(lang('Pattern is required'), 'pattern');
        } // if
        
        if ((strpos($posted_pattern, INVOICE_NUMBER_COUNTER_TOTAL) === false) &&
            (strpos($posted_pattern, INVOICE_NUMBER_COUNTER_YEAR) === false) &&
            (strpos($posted_pattern, INVOICE_NUMBER_COUNTER_MONTH) === false)) {
          $errors->addError(lang('One of invoice counters is required (:total, :year, :month)', array('total' => INVOICE_NUMBER_COUNTER_TOTAL,'year' => INVOICE_NUMBER_COUNTER_YEAR,'month' => INVOICE_NUMBER_COUNTER_MONTH)), 'pattern');
        } // if
        
        if ($errors->hasErrors()) {
          $this->smarty->assign(array(
            'errors' => $errors
          ));
        }  else {
          Invoices::setInvoiceNumberGeneratorPattern($posted_pattern);
          flash_success('Pattern for invoice number generator is saved');
          $this->redirectTo('admin');
        } // if
      } // if
    } // index
    
  } // InvoiceItemTemplatesAdminController


?>