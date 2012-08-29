<?php
  
  /**
   * Dashboard controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class DashboardController extends ApplicationController {
    
    /**
     * Show dashboard overview
     *
     * @param void
     * @return null
     */
    function index() {
      
      // Welcome message
      if(ConfigOptions::getValue('show_welcome_message')) {
        $this->wireframe->addPageAction(lang('Hide Welcome Message'), assemble_url('admin_settings_hide_welcome_message'), null, array(
          'method' => 'post',
          'confirm' => lang('You are about to hide welcome message. If you wish to bring it back later on you can do it from General settings page in Administration. Hide now?'),
        ));
        $this->smarty->assign('show_welcome_message', true);
        
      // Regular dashboard
      } else {
        if(Project::canAdd($this->logged_user)) {
          $this->wireframe->addPageAction(lang('New Project'), assemble_url('projects_add'));
        } // if
        
        $this->wireframe->addRssFeed(
          $this->owner_company->getName() . ' - ' . lang('Recent activities'),
          assemble_url('rss', array('token' => $this->logged_user->getToken(true))),
          FEED_RSS          
        );
        
        $pinned_project_ids = PinnedProjects::findProjectIdsByUser($this->logged_user);
        if(is_foreachable($pinned_project_ids)) {
          $pinned_projects = Projects::findByIds($pinned_project_ids);
        } else {
          $pinned_projects = null;
        } // if
        
        $dashboard_sections = new NamedList();
        event_trigger('on_dashboard_sections', array(&$dashboard_sections, &$this->logged_user));
        
        $important_items = new NamedList();
        event_trigger('on_dashboard_important_section',array(&$important_items, &$this->logged_user));
        
        $this->smarty->assign(array(
          'show_welcome_message' => false,
          'important_items' => $important_items,
          'pinned_projects' => $pinned_projects,
          'dashboard_sections' => $dashboard_sections,
          'online_users' => Users::findWhoIsOnline($this->logged_user),
          'grouped_activities' => group_by_date(ActivityLogs::findActiveProjectsActivitiesByUser($this->logged_user, 20), $this->logged_user),
        ));
      } // if
    } // index
    
    /**
     * Trashed Project Objects
     *
     * @param void
     * @return null
     */
    function trash() {
      $this->wireframe->current_menu_item = 'trash';
      
      if(!$this->logged_user->isAdministrator() && !$this->logged_user->getSystemPermission('manage_trash')) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        $action = $this->request->post('action');
        if(!in_array($action, array('restore', 'delete'))) {
          $this->httpError(HTTP_ERR_BAD_REQUEST, 'Invalid action');
        } // if
        
        $object_ids = $this->request->post('objects');
        $objects = ProjectObjects::findByIds($object_ids, STATE_DELETED, VISIBILITY_PRIVATE);
        
        db_begin_work();
        foreach($objects as $object) {
          if($action == 'restore') {
            $object->restoreFromTrash();
          } else {
            $object->delete();
          } // if
        } // foreach
        db_commit();
      } // if
      
      $per_page = 30;
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      
      list($objects, $pagination) = ProjectObjects::paginateTrashed($this->logged_user, $page, $per_page);
    	$this->smarty->assign(array(
    	  'objects' => $objects,
    	  'pagination' => $pagination,
    	));
    	
    	if(is_foreachable($objects)) {
    	  $this->wireframe->addPageAction(lang('Empty Trash'), assemble_url('trash_empty'), null, array(
    	    'method' => 'post',
    	    'confirm' => lang('Are you sure that you want to empty trash?'),
    	  ));
    	} // if
    } // trash
    
    /**
     * Delete permanently all items that are in trash
     *
     * @param void
     * @return null
     */
    function trash_empty() {
      if(!$this->logged_user->isAdministrator() && !$this->logged_user->getSystemPermission('manage_trash')) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $operations_performed = 0;

      $objects = ProjectObjects::findTrashed($this->logged_user);
      if(is_foreachable($objects)) {
        db_begin_work();
        foreach($objects as $object) {
          $delete = $object->delete();
          if($delete && !is_error($delete)) {
            $operations_performed++;
          } // if
        } // foreach
        db_commit();

        flash_success(':count objects deleted', array('count' => $operations_performed));
      } else {
        flash_success('Already empty');
      } // if

      $this->redirectTo('trash');
    } // trash_empty
    
    /**
     * Starred Project Objects
     *
     * @param void
     * @return null
     */
    function starred() {
      $this->wireframe->current_menu_item = 'starred_folder';
      if($this->request->isSubmitted()) {
        $action = $this->request->post('action');
        if(!in_array($action, array('unstar', 'unstar_and_complete', 'trash'))) {
          $this->httpError(HTTP_ERR_BAD_REQUEST, 'Invalid action');
        } // if
        
        $objects = ProjectObjects::findByIds($this->request->post('objects'), STATE_VISIBLE, $this->logged_user->getVisibility());
        
        db_begin_work();
        foreach($objects as $object) {
          
          // Unstar selected object
          if($action == 'unstar') {
            $object->unstar($this->logged_user);
            
          // Unstar and marked as completed
          } elseif($action == 'unstar_and_complete') {
            $operation = $object->unstar($this->logged_user);
            if($operation && !is_error($operation)) {
              if($object->can_be_completed) {
                $object->complete($this->logged_user);
              } // if
            } // if
            
          // Move to Trash
          } elseif($action == 'trash') {
            if(!$object->canDelete($this->logged_user)) {
              continue;
            } // if
            
            $object->moveToTrash();
          } // if
          
        } // foreach
        db_commit();
      } // if
      
    	$this->smarty->assign('objects', StarredObjects::findByUser($this->logged_user));
    	
    	if($this->request->get('async')) {
        $this->smarty->display(get_template_path('starred', 'dashboard', SYSTEM_MODULE));
        die();
      } // if
    } // starred
    
    /**
     * Search
     *
     * @param void
     * @return null
     */
    function search() {
      $this->wireframe->current_menu_item = 'search';
      $this->smarty->assign('search_url', assemble_url('search'));
      
      $search_for = trim($this->request->get('q'));
      $search_type = $this->request->get('type');
      
      $per_page = 30;
      
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      
      if($search_for && $search_type) {
      
        // Search inside the project
        if($search_type == 'in_projects') {
          list($results, $pagination) = search_index_search($search_for, 'ProjectObject', $this->logged_user, $page, $per_page);
          
        // Search for people
        } elseif($search_type == 'for_people') {
          list($results, $pagination) = search_index_search($search_for, 'User', $this->logged_user, $page, $per_page);
          
        // Search for projects
        } elseif($search_type == 'for_projects') {
          list($results, $pagination) = search_index_search($search_for, 'Project', $this->logged_user, $page, $per_page);
          
        // Unknown type
        } else {
          $search_for = '';
          $search_type = null;
        } // if
      
      } else {
        $search_for = '';
        $search_type = null;
      } // if
      
      $this->smarty->assign(array(
        'search_for'     => $search_for,
        'search_type'    => $search_type,
        'search_results' => $results,
        'pagination'     => $pagination
      ));
    } // search
    
    /**
     * Render and process quick search dialog
     *
     * @param void
     * @return null
     */
    function quick_search() {
      if(!$this->request->isAsyncCall()) {
        $this->redirectTo('search');
      } // if
      
      if($this->request->isSubmitted()) {
        $search_for = trim($this->request->post('search_for'));
        $search_type = $this->request->post('search_type');
        
        if($search_for == '') {
          die(lang('Nothing to search for'));
        } // if
        
        $this->smarty->assign(array(
          'search_for' => $search_for,
          'search_type' => $search_type,
        ));
        $per_page = 5;
        
        // Search inside the project
        if($search_type == 'in_projects') {
          $template = get_template_path('_quick_search_project_objects', null, SYSTEM_MODULE);
          list($results, $pagination) = search_index_search($search_for, 'ProjectObject', $this->logged_user, 1, $per_page);
          
        // Search for people
        } elseif($search_type == 'for_people') {
          $template = get_template_path('_quick_search_users', null, SYSTEM_MODULE);
          list($results, $pagination) = search_index_search($search_for, 'User', $this->logged_user, 1, $per_page);
          
        // Search for projects
        } elseif($search_type == 'for_projects') {
          $template = get_template_path('_quick_search_projects', null, SYSTEM_MODULE);
          list($results, $pagination) = search_index_search($search_for, 'Project', $this->logged_user, 1, $per_page);
          
        // Unknown type
        } else {
          die(lang('Unknown search type: :type', array('type' => $search_type)));
        } // if
        
        $this->smarty->assign(array(
          'results' => $results,
          'pagination' => $pagination,
        ));
        
        $this->smarty->display($template);
        die();
      }
    } // quick_search
    
    /**
     * Show recent activities page
     *
     * @param void
     * @return null
     */
    function recent_activities() {
      $this->skip_layout = $this->request->isAsyncCall();
      
      $this->smarty->assign(array(
        'grouped_activities' => group_by_date(ActivityLogs::findActiveProjectsActivitiesByUser($this->logged_user, 20), $this->logged_user),
      ));
    } // recent_activities
    
    /**
     * Show active projects list - all projects in a brief view
     *
     * @param void
     * @return null
     */
    function active_projects() {
      if(!$this->request->isAsyncCall()) {
        $this->redirectTo('projects');
      } // if
      
      $this->skip_layout = true;
      $this->smarty->assign('projects', Projects::findByUser($this->logged_user, array(PROJECT_STATUS_ACTIVE)));
    } // active_projects
    
    /**
     * Show objects posted since users last visit
     *
     * @param void
     * @return null
     */
    function new_since_last_visit() {
      $this->skip_layout = $this->request->isAsyncCall();
      
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      
      list($objects, $pagination) = ProjectObjects::paginateNew($this->logged_user, $page, 10);
      
      $this->smarty->assign(array(
        'objects'    => $objects,
        'pagination' => $pagination,
      ));
    } // new_since_last_visit
    
    /**
     * Mark all items as read (update users last visit timestamp)
     *
     * @param void
     * @return null
     */
    function mark_all_read() {
      if($this->request->isSubmitted()) {
        $this->logged_user->setLastVisitOn(DateTimeValue::now());
        $save = $this->logged_user->save();
        
        if($save && !is_error($save)) {
          if($this->request->isAsyncCall()) {
            $this->httpOk();
          } else {
            flash_success('All new items are marked as read');  
          } // if
        } else {
          $message = lang('Failed to mark new items as read');
          if ($this->request->isAsyncCall()) {
            $this->httpError(HTTP_ERR_OPERATION_FAILED, $message);
            die();
          } else {
            flash_success($message);
          } // if
        } // if
        $this->redirectToReferer(assemble_url('dashboard'));
      } else {
        $this->httpError(HTTP_BAD_REQUEST);
      } // if
    } // mark_all_read
    
    /**
     * Show objects that are late or scheduled for today for a given user
     *
     * @param void
     * @return null
     */
    function late_today() {
      $page = (integer) $this->request->get('page');
      if($page < 1) {
        $page = 1;
      } // if
      
      list($objects, $pagination) = ProjectObjects::findLateAndToday($this->logged_user, null, get_completable_project_object_types(), $page, 30);
      
      $this->smarty->assign(array(
        'objects' => $objects,
        'pagination' => $pagination,
      ));
      
      if($this->request->isAsyncCall()) {
        $this->smarty->display(get_template_path('late_today', 'dashboard', SYSTEM_MODULE));
        die();
      } // if
    } // late_today
    
    /**
     * Render recent activities feed
     *
     * @param void
     * @return null
     */
    function rss() {
      require_once ANGIE_PATH . '/classes/feed/init.php';
      
      $projects = Projects::findNamesByUser($this->logged_user);
      
      $feed = new Feed($this->owner_company->getName() . ' - ' . lang('Recent activities'), ROOT_URL);
      $feed->setDescription(lang('Recent activities in active projects'));
      
      $activities = ActivityLogs::findActiveProjectsActivitiesByUser($this->logged_user, 50);
      if(is_foreachable($activities)) {
        foreach($activities as $activity) {
          $object = $activity->getObject();
          $activity_title = $activity_body = $activity->renderHead();
          $activity_title = strip_tags($activity_title);
          
          if ($activity->has_body && ($body = trim($activity->renderBody()))) {
            $activity_body.=$body;
          } // if
          
          $item = new FeedItem($activity_title, $object->getViewUrl(), $activity_body, $activity->getCreatedOn());
          $item->setId(extend_url($object->getViewUrl(), array('guid' => $activity->getId())));
          $feed->addItem($item);
        } // foreach
      } // if
      
      print render_rss_feed($feed);
      die();
    } // rss
    
    /**
     * Render global iCalendar feed
     *
     * @param void
     * @return null
     */
    function ical() {
    	$filter = ProjectUsers::getVisibleTypesFilter($this->logged_user, array(PROJECT_STATUS_ACTIVE), get_completable_project_object_types());
      if($filter) {
        $objects = ProjectObjects::find(array(
    		  'conditions' => array($filter . ' AND completed_on IS NULL AND state >= ? AND visibility >= ?', STATE_VISIBLE, $this->logged_user->getVisibility()),
    		  'order'      => 'priority DESC',
    		));
    		
    		render_icalendar(lang('Global Calendar'), $objects, true);
    		die();
      } else {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
    } // ical
    
    /**
     * Show ical subscribe page
     *
     * @param void
     * @return null
     */
    function ical_subscribe() {
    	$this->wireframe->print_button = false;
      
      $ical_url = assemble_url('ical', array(
        'token' => $this->logged_user->getToken(true),
      ));
      
      $ical_subscribe_url = str_replace(array('http://', 'https://'), array('webcal://', 'webcal://'), $ical_url);
      
      $this->smarty->assign(array(
        'ical_url' => $ical_url,
        'ical_subscribe_url' => $ical_subscribe_url
      ));
    } // ical_subscribe
    
    /**
     * Show quick add form
     *
     * @param void
     * @return null
     */
    function quick_add() {
      $this->wireframe->current_menu_item = 'quick_add';
      
      $quick_add_urls = array();
      event_trigger('on_quick_add', array(&$quick_add_urls));
      $all_projects_permissions = array_keys($quick_add_urls);
      
      $formatted_map = array();
      $projects_roles_map = ProjectUsers::getProjectRolesMap($this->logged_user, array(PROJECT_STATUS_ACTIVE));
      if(!is_foreachable($projects_roles_map)) {
        print lang('There are no active projects that you are involved with');
        die();
      } // if
      
      if (is_foreachable($projects_roles_map)) {
        foreach ($projects_roles_map as $project_id => $project_role_map) {
        	$formatted_map[$project_id] = array(
        	  'name' => array_var($project_role_map, 'name')
        	);
        	$project_leader = array_var($project_role_map, 'leader');
        	$project_role_permissions = array_var($project_role_map, 'permissions', null);
        	
        	if ($this->logged_user->isAdministrator() || $this->logged_user->isProjectManager() || ($this->logged_user->getId() == $project_leader)) {
          	foreach ($all_projects_permissions as $current_permission) {
          		$formatted_map[$project_id]['permissions'][] = array(
          		  'title' => lang($current_permission),
          		  'name' => $current_permission
          		);
          	} // if
        	} else {
        	  foreach ($all_projects_permissions as $current_permission) {
          	  if (array_var($project_role_permissions, $current_permission, 0) > 1) {
          	    $formatted_map[$project_id]['permissions'][] = array(
            		  'title' => lang($current_permission),
            		  'name' => $current_permission
            		);
          	  } // if
          	} // if
        	} // if
        } // foreach
      } // if
            
      $this->smarty->assign(array(
        'formatted_map' => $formatted_map,
        'quick_add_url' => $quick_add_url,
        'js_encoded_formatted_map' => do_json_encode($formatted_map),
        'js_encoded_quick_add_urls' => do_json_encode($quick_add_urls)
      ));
    } // quick_add
    
    /**
     * Show JavaScript disabled page
     *
     * @param void
     * @return null
     */
    function js_disabled() {
      
    } // js_disabled
    
  }

?>