<?php
  // we need admin controller
  use_controller('admin');
  
  /**
   * Tax Rates admin controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class TaxRatesAdminController extends AdminController {

    /**
     * Selected tax rate
     *
     * @var TaxRate
     */
    var $active_tax_rate;

    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'tax_rates_admin';

    /**
     * Contruct tax rates controller
     *
     * @param Request $request
     * @return TaxRatesAdminController
     */
    function __construct($request) {
      parent::__construct($request);

      $tax_rate_id = $this->request->getId('tax_rate_id');
      if($tax_rate_id) {
        $this->active_tax_rate = TaxRates::findById($tax_rate_id);
      } // if

      if(!instance_of($this->active_tax_rate, 'TaxRate')) {
        $this->active_tax_rate = new TaxRate();
      } // if

      $add_tax_rate_url = assemble_url('admin_tax_rate_add');

      $this->wireframe->addBreadCrumb(lang('Tax Rates'), assemble_url('admin_tax_rates'));
      $this->wireframe->addPageAction(lang('New Tax Rate'), $add_tax_rate_url);

      $this->smarty->assign(array(
        'active_tax_rate' => $this->active_tax_rate,
        'add_tax_rate_url' => $add_tax_rate_url,
      ));
    } // __construct

    /**
     * Show all available currencies
     *
     * @param void
     * @return null
     */
    function index() {
      $this->smarty->assign('tax_rates', TaxRates::findAll());
    } // index

    /**
     * Create new currency
     *
     * @param void
     * @return null
     */
    function add() {
      $tax_rate_data = $this->request->post('tax_rate');
      $this->smarty->assign('tax_rate_data', $tax_rate_data);

      if($this->request->isSubmitted()) {
        $this->active_tax_rate->setAttributes($tax_rate_data);
        $save = $this->active_tax_rate->save();

        if($save && !is_error($save)) {
          flash_success('Tax Rate ":name" has been created', array('name' => $this->active_tax_rate->getName()));
          $this->redirectTo('admin_tax_rates');
        } else {
          $this->smarty->assign('errors', $save);
        } // if
      } // if
    } // add

    /**
     * Update existing route
     *
     * @param void
     * @return null
     */
    function edit() {
      if($this->active_tax_rate->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_tax_rate->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->wireframe->addPageMessage(lang('Updating of this tax rate will also update all existing invoices. If that is not an option, consider creating a new tax rate'), 'warning');

      $tax_rate_data = $this->request->post('tax_rate');
      if(!is_array($tax_rate_data)) {
        $tax_rate_data = array(
          'name' => $this->active_tax_rate->getName(),
          'percentage' => $this->active_tax_rate->getPercentage(),
        );
      } // if
      $this->smarty->assign('tax_rate_data', $tax_rate_data);

      if($this->request->isSubmitted()) {
        $this->active_tax_rate->setAttributes($tax_rate_data);
        $save = $this->active_tax_rate->save();

        if($save && !is_error($save)) {
          flash_success('Tax rate ":name" has been updated', array('name' => $this->active_tax_rate->getName()));
          $this->redirectTo('admin_tax_rates');
        } else {
          $this->smarty->assign('errors', $save);
        } // if
      } // if
    } // edit

    /**
     * Delete existing currency
     *
     * @param void
     * @return null
     */
    function delete() {
      if($this->active_tax_rate->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_tax_rate->canDelete($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if

      if($this->request->isSubmitted()) {
        $delete = $this->active_tax_rate->delete();
        if($delete && !is_error($delete)) {
          flash_success('Tax rate ":name" has been deleted', array('name' => $this->active_tax_rate->getName()));
        } else {
          flash_error('Failed to delete ":name" tax rate', array('name' => $this->active_tax_rate->getName()));
        } // if

        $this->redirectTo('admin_tax_rates');
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // delete

  }

?>