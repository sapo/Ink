<?php

  // Extend admin controller
  use_controller('admin');
  
  /**
   * Currencies administration controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class CurrenciesAdminController extends AdminController {
    
    /**
     * Selected currency
     *
     * @var Currency
     */
    var $active_currency;
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'currencies_admin';
    
    /**
     * Construct currencies admin controller
     *
     * @param Request $request
     * @return CurrenciesAdminController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $currency_id = $this->request->getId('currency_id');
      if($currency_id) {
        $this->active_currency = Currencies::findById($currency_id);
      } // if
      
      if(!instance_of($this->active_currency, 'Currency')) {
        $this->active_currency = new Currency();
      } // if
      
      $add_currency_url = assemble_url('admin_currencies_add');
      
      $this->wireframe->addBreadCrumb(lang('Currencies'), assemble_url('admin_currencies'));
      $this->wireframe->addPageAction(lang('New Currency'), $add_currency_url);
      
      $this->smarty->assign(array(
        'active_currency' => $this->active_currency,
        'add_currency_url' => $add_currency_url,
      ));
    } // __construct
    
    /**
     * Show all available currencies
     *
     * @param void
     * @return null
     */
    function index() {
      $this->smarty->assign('currencies', Currencies::findAll());
    } // index
    
    /**
     * Create new currency
     *
     * @param void
     * @return null
     */
    function add() {
      $currency_data = $this->request->post('currency');
      $this->smarty->assign('currency_data', $currency_data);
      
      if($this->request->isSubmitted()) {
        $this->active_currency->setAttributes($currency_data);
        $save = $this->active_currency->save();
        
        if($save && !is_error($save)) {
          flash_success('Currency ":name" has been created', array('name' => $this->active_currency->getName()));
          $this->redirectTo('admin_currencies');
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
      if($this->active_currency->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $currency_data = $this->request->post('currency');
      if(!is_array($currency_data)) {
        $currency_data = array(
          'name' => $this->active_currency->getName(),
          'code' => $this->active_currency->getCode(),
          'default_rate' => $this->active_currency->getDefaultRate(),
        );
      } // if
      $this->smarty->assign('currency_data', $currency_data);
      
      if($this->request->isSubmitted()) {
        $this->active_currency->setAttributes($currency_data);
        $save = $this->active_currency->save();
        
        if($save && !is_error($save)) {
          flash_success('Currency ":name" has been updated', array('name' => $this->active_currency->getName()));
          $this->redirectTo('admin_currencies');
        } else {
          $this->smarty->assign('errors', $save);
        } // if
      } // if
    } // edit
    
    /**
     * Set currency as default
     *
     * @param void
     * @return null
     */
    function set_as_default() {
      if($this->active_currency->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->request->isSubmitted()) {
        $update = Currencies::setDefault($this->active_currency);
        if($update && !is_error($update)) {
          $this->httpOk();
        } else {
          $this->serveData($update);
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // set_as_default
    
    /**
     * Delete existing currency
     *
     * @param void
     * @return null
     */
    function delete() {
      if($this->active_currency->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->request->isSubmitted()) {
        $delete = $this->active_currency->delete();
        if($delete && !is_error($delete)) {
          flash_success('Currency ":name" has been deleted', array('name' => $this->active_currency->getName()));
        } else {
          flash_error('Failed to delete ":name" currency', array('name' => $this->active_currency->getName()));
        } // if
        
        $this->redirectTo('admin_currencies');
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // delete
    
  }

?>