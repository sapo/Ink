<?php

  // Build on top of administration controller
  use_controller('settings', SYSTEM_MODULE);

  /**
   * Manage email settings
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class EmailTemplatesAdminController extends SettingsController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'email_templates_admin';
    
    /**
     * Selected email template
     *
     * @var EmailTemplate
     */
    var $active_template;
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return EmailTemplatesAdminController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->wireframe->addBreadCrumb(lang('Email templates'), assemble_url('admin_settings_email_templates'));
      
      $module_name = $this->request->get('module_name');
      $template_name = $this->request->get('template_name');
      
      if($module_name && $template_name) {
        $this->active_template = EmailTemplates::findById(array(
          'name' => $template_name,
          'module' => $module_name,
        ));
      } // if
      
      if(instance_of($this->active_template, 'EmailTemplate')) {
        $this->wireframe->addBreadCrumb($this->active_template->getModule() . ' / ' . $this->active_template->getName(), $this->active_template->getUrl());
      } else {
        $this->active_template = new EmailTemplate();
      } // if
      
      $this->smarty->assign('active_template', $this->active_template);
    } // __construct
    
    /**
     * List all avilable email tempaltes and let users manage them
     *
     * @param void
     * @return null
     */
    function index() {
      $this->smarty->assign('grouped_templates', EmailTemplates::findGrouped());
    } // index
    
    /**
     * Show template details page
     *
     * @param void
     * @return null
     */
    function details() {
      if($this->active_template->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $template_data = $this->request->post('template');
      if(!is_array($template_data)) {
        $template_data = array(
          'subject' => $this->active_template->getSubject(),
          'body' => $this->active_template->getBody(),
        );
      } // if
      
      $this->smarty->assign(array(
        'template'      => $template,
        'template_data' => $template_data,
        'languages'     => Languages::findAll(),
      ));
      
      if($this->request->isSubmitted()) {
        $this->active_template->setAttributes($template_data);
        
        $save = $template->save();
        if($save && !is_error($save)) {
          flash_success('Email template has been updated');
          $this->redirectTo('admin_settings_email_templates');
        } else {
          $this->smarty->assign('errors', $save);;
        } // if
      } // if
    } // details
    
    /**
     * Show / process email template form
     *
     * @param void
     * @return null
     */
    function edit() {
      if($this->active_template->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $locale = $this->request->get('locale', null);
      
      $template_data = $this->request->post('template');
      if(!is_array($template_data)) {
        $template_data = array(
          'subject' => $this->active_template->getSubject($locale),
          'body' => $this->active_template->getBody($locale),
        );
      } // if
      
      $template_variables = $this->active_template->getVariables() ? explode("\n", $this->active_template->getVariables()) : null;
      
      $this->smarty->assign(array(
        'template_data' => $template_data,
        'template_variables' => $template_variables,
        'locale' => $locale,
      ));
      
      if($this->request->isSubmitted()) {
        if($locale) {
          $this->active_template->writeLocaleProperties(array_var($template_data, 'subject'), array_var($template_data, 'body'), $locale);
        } else {
          $this->active_template->setAttributes($template_data);
        } // if
        
        $save = $this->active_template->save();
        if($save && !is_error($save)) {
          flash_success('Email template has been updated');
          $this->redirectToUrl($this->active_template->getUrl());
        } else {
          $this->smarty->assign('errors', $save);;
        } // if
      } // if
    } // template
  
  } // EmailTemplatesAdminController

?>