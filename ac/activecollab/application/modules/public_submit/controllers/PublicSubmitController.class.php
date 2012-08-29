<?php
  // we need AppicationController
  use_controller('application');
  
  /**
   * PublicSubmit Public Controller
   *
   * @package activeCollab.modules.public_submit
   * @subpackage controllers
   */
  class PublicSubmitController extends ApplicationController {
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'public_submit';
    
    /**
     * Active project (determined in admin settings)
     *
     * @var Project
     */
    var $active_project;
    
    /**
     * Is captcha enabled?
     *
     * @var boolean
     */
    var $captcha_enabled = false;
    
    /**
     * User is not required to log in
     *
     * @var boolean
     */
    var $login_required = false;
    
    /**
     * Construct method
     *
     * @param string $request
     * @return PublicSubmitController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if (!ConfigOptions::getValue('public_submit_enabled')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->setLayout(array(
        "module" => PUBLIC_SUBMIT_MODULE,
        "layout" => 'wireframe',
      ));
      
      $this->active_project = Projects::findById(ConfigOptions::getValue('public_submit_default_project'));
      if(!instance_of($this->active_project, 'Project')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      // check if captcha is enabled, and if it is, check that GD library is loaded
      $this->captcha_enabled = (boolean) ConfigOptions::getValue('public_submit_enable_captcha');
      if ($this->captcha_enabled) {
        if (!(extension_loaded('gd') || extension_loaded('gd2')) || !function_exists('imagefttext')) {
        	$this->captcha_enabled = false;
        } // if
      } // if
      
      $this->smarty->assign(array(
        "active_project" => $this->active_project,
        "submit_ticket_url" => assemble_url('public_submit'),
        'captcha_enabled' => $this->captcha_enabled,
      ));
    } // __construct
    
    /**
     * Index page action
     *
     */
    function index() {
      if (!module_loaded('tickets')) {
        $this->redirectTo('public_submit_unavailable');
      } // if
      
      $ticket_data = $this->request->post('ticket');
      $this->smarty->assign(array(
        'captcha_url' => ROOT_URL.'/captcha.php?id='.md5(time()),
        "ticket_data" => $ticket_data,
      ));
            
      if ($this->request->isSubmitted()) {
        $errors = new ValidationErrors();
        
        if ($this->captcha_enabled) {
          //$captcha_value = array_var($_SESSION, CAPTCHA_SESSION_ID);
          if (!Captcha::Validate($ticket_data['captcha'])) {
            $errors->addError(lang('Code you entered is not valid'), 'captcha');
            $this->smarty->assign('errors', $errors);
          } // if
        } // if
        
        if (!$errors->hasErrors()) {
          $submitter = new AnonymousUser($ticket_data['created_by_name'], $ticket_data['created_by_email']);
          
          db_begin_work();
          $ticket = new Ticket();
          
          attach_from_files($ticket, $submitter);

          $ticket->setAttributes($ticket_data);
          $ticket->setProjectId($this->active_project->getId());
          $ticket->setVisibility(VISIBILITY_NORMAL);
          $ticket->setState(STATE_VISIBLE);
          $ticket->setCreatedBy($submitter);

          $save = $ticket->save();
          if (!$save || is_error($save)) {
            unset($ticket_data['captcha']);
            db_rollback();
            $this->smarty->assign(array(
              'ticket_data' => $ticket_data,
              'errors' => $save,
            ));
          } else {
            Subscriptions::subscribeUsers(array($this->active_project->getLeaderId()), $ticket);
            db_commit();
            $ticket->ready();

            $this->redirectTo('public_submit_success');
          } // if
        } // if
      } // if
    } // index
    
    /**
     * Action if public_submit module is not setuped properly
     *
     */
    function unavailable() {
      
    } // unavailable
    
    /**
     * Ticket submitted successfully
     *
     */
    function success() {
      
    } // succes
    
  } // PublicSubmitController
?>