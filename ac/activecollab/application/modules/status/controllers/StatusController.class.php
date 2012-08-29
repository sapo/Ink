<?php

  // We need projects controller
  use_controller('application', SYSTEM_MODULE);

  /**
   * Status controller
   *
   * @package activeCollab.modules.status
   * @subpackage controllers
   */
  class StatusController extends ApplicationController {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = STATUS_MODULE;
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'status';
    
    /**
     * Array of available API actions
     *
     * @var array
     */
    var $api_actions = array('index', 'add');
    
    /**
     * Status updates per page count
     * 
     * @var integer
     */
    var $status_updates_per_page = 15;
    
    /**
     * Constructor method
     *
     * @param string $request
     * @return StatusController
     */
    function __construct($request) {
      parent::__construct($request);
      
      $this->wireframe->current_menu_item = 'status';
      
      if(!$this->logged_user->isAdministrator() && !$this->logged_user->getSystemPermission('can_use_status_updates')) {
        if($this->request->getAction() == 'count_new_messages') {
          die('0');
        } else {
          $this->httpError(HTTP_ERR_FORBIDDEN);
        } // if
      } // if
      
      $this->smarty->assign(array(
        "add_status_message_url" => assemble_url('status_updates_add'),
      ));
    } // __construct
    
    /**
     * Index page action
     * 
     * @param void
     * @return void
     */
    function index() {
      UserConfigOptions::setValue('status_update_last_visited', new DateTimeValue(), $this->logged_user);
      
      // Popup
      if($this->request->isAsyncCall()) {
        $this->skip_layout = true;
        
        $this->setTemplate(array(
          'template' => 'popup',
          'controller' => 'status',
          'module' => STATUS_MODULE,
        ));
        
        $last_visit = UserConfigOptions::getValue('status_update_last_visited', $this->logged_user);
        $new_messages_count = StatusUpdates::countNewMessagesForUser($this->logged_user, $last_visit);
        
        $limit = $new_messages_count > 10 ? $new_messages_count : 10;
        
        $latest_status_updates = StatusUpdates::findVisibleForUser($this->logged_user, $limit);
        $this->smarty->assign(array(
          'status_updates' => $latest_status_updates,
          "rss_url" => assemble_url('status_updates_rss', array(
            'token' => $this->logged_user->getToken(true),
           )),
        ));
        
      // Archive
      } else {
        $this->setTemplate(array(
          'template' => 'messages',
          'controller' => 'status',
          'module' => STATUS_MODULE,
        ));
      
        $visible_users = $this->logged_user->visibleUserIds();
        $selected_user_id = $this->request->getId('user_id');
        if($selected_user_id) {
          if(!in_array($selected_user_id, $visible_users)) {
            $this->httpError(HTTP_ERR_FORBIDDEN);
          } // if
          
          $selected_user = Users::findById($selected_user_id);
          if(!instance_of($selected_user, 'User')) {
            $this->httpError(HTTP_ERR_NOT_FOUND);
          } // if
        } else {
          $selected_user = null;
        } // if
        
        if($this->request->isApiCall()) {
          if($selected_user) {
            $this->serveData(StatusUpdates::findByUser($selected_user), 'messages');
          } else {
            $this->serveData(StatusUpdates::findVisibleForUser($this->logged_user, 50), 'messages');
          } // if
        } else {
          $per_page = $this->status_updates_per_page; // Messages per page
          $page = (integer) $this->request->get('page');
          if($page < 1) {
            $page = 1;
          } // if
          
          if($selected_user) {
            $rss_url = assemble_url('status_updates_rss', array(
                "user_id" => $selected_user_id,
                'token' => $this->logged_user->getToken(true),
            ));
            $rss_title = clean($selected_user->getDisplayName()). ': '.lang('Status Updates');
            list($status_updates, $pagination) = StatusUpdates::paginateByUser($selected_user, $page, $per_page);
            $this->smarty->assign(array(
              "selected_user" => $selected_user,
              "rss_url" => $rss_url
            ));
          } else {
            $rss_url = assemble_url('status_updates_rss', array('token' => $this->logged_user->getToken(true)));
            $rss_title = lang('Status Updates');
            list($status_updates, $pagination) = StatusUpdates::paginateByUserIds($visible_users, $page, $per_page);
            $this->smarty->assign(array(
              "rss_url" => $rss_url
            ));
          } // if
          
          $this->wireframe->addRssFeed(
            $rss_title,
            $rss_url,
            FEED_RSS          
          );
          
          $this->smarty->assign(array(
            "visible_users" => Users::findUsersDetails($visible_users),
            "status_updates" => $status_updates,
            "pagination" => $pagination
          ));
        } // if
      } // if
    } // index
    
    /**
     * Add status message
     * 
     * @param void
     * @return void
     */
    function add() {
      if(!$this->request->isApiCall() && !$this->request->isAsyncCall()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      $this->wireframe->print_button = false;
      
      if($this->request->isSubmitted()) {
        $status_data = $this->request->post('status');
        
        $status = new StatusUpdate();
        
        $status->setAttributes($status_data);
        $status->setCreatedById($this->logged_user->getId());
        $status->setCreatedByName($this->logged_user->getName());
        $status->setCreatedByEmail($this->logged_user->getEmail());
        
        $save = $status->save();
        if(!$save || is_error($save)) {
          if($this->request->isApiCall()) {
            $this->serveData($save);
          } else {
            $this->httpError(HTTP_ERR_OPERATION_FAILED);
          } // if
        } // if
        
        if($this->request->isApiCall()) {
          $this->serveData($status, 'message');
        } else {
          UserConfigOptions::setValue('status_update_last_visited', new DateTimeValue(), $this->logged_user);
          
          $this->smarty->assign('status_update', $status);
          print $this->smarty->fetch(get_template_path('_status_row', $this->controller_name, STATUS_MODULE));
          die();
        } // if
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // add
    
    /**
     * Provide ajax functionality for menu badge
     * 
     * @param void
     * @return void
     */
    function count_new_messages() {
      $last_visit = UserConfigOptions::getValue('status_update_last_visited', $this->logged_user);
      echo StatusUpdates::countNewMessagesForUser($this->logged_user, $last_visit);
      die();
    } // count_new_messages
    
    /**
     * Rss for status updates
     * 
     * @param void
     * @return void
     */
    function rss() {
      require_once ANGIE_PATH . '/classes/feed/init.php';
      
      $archive_url = assemble_url('status_updates');
    	
    	$selected_user = $this->request->get('user_id');
    	if ($selected_user) {
        if (!in_array($selected_user, $this->logged_user->visibleUserIds())) {
          $this->httpError(HTTP_ERR_FORBIDDEN);
        } // if
    	  
    	  $user = Users::findById($selected_user);
    	  if (!instance_of($user, 'User')) {
    	    $this->httpError(HTTP_ERR_NOT_FOUND);
    	  } // if
    	  
    	  $archive_url = assemble_url('status_updates', array(
      	  'user_id' => $user->getId(),
    	  ));
    	  $latest_status_updates = StatusUpdates::findByUser($user, 20);
    	  $feed = new Feed($user->getDisplayName(). ': '.lang('Status Updates'), $archive_url);
    	} else {
      	$latest_status_updates = StatusUpdates::findVisibleForUser($this->logged_user, 20);
      	$feed = new Feed(lang('Status Updates'), $archive_url);
    	} // if
    	
    	if(is_foreachable($latest_status_updates)) {
    	   foreach ($latest_status_updates as $status_update) {
    	     $item = new FeedItem($status_update->getCreatedByName().': '.str_excerpt($status_update->getMessage(), 20), $status_update->getViewUrl(), $status_update->getMessage(), $status_update->getCreatedOn());
    	     $item->setId($status_update->getId());
    	     $feed->addItem($item);
    	   } // foreach
    	} // if
    	
      print render_rss_feed($feed);
      die();
    } // rss
    
    /**
     * finds url to display status update
     * 
     * @param null
     * @return null
     */
    function view() {
      $status_update = StatusUpdates::findById($this->request->get('status_update_id'));
      
      if (!instance_of($status_update, 'StatusUpdate')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $this->redirectToUrl($status_update->getRealViewUrl($this->status_updates_per_page));
      die();
    } // view
    
  } // StatusController
?>