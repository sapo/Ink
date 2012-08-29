<?php

  // Use assignments controller
  use_controller('assignments', RESOURCES_MODULE);

  /**
   * Assignment filters controler
   *
   * @package activeCollab.modules.resources
   * @subpackage controllers
   */
  class AssignmentFiltersController extends AssignmentsController {
    
    /**
     * PHP4 friendly controller name
     *
     * @var string
     */
    var $controller_name = 'assignment_filters';
    
    /**
     * Construct filters controller
     *
     * @param Request $request
     * @return AssignmentFiltersController
     */
    function __construct($request) {
    	parent::__construct($request);
    	
    	if($this->active_filter->isLoaded() && ($this->request->getAction() != 'add')) {
    	  $this->wireframe->addBreadCrumb($this->active_filter->getName(), $this->active_filter->getUrl());
    	} // if
    } // __construct
    
    /**
     * Show filter details
     *
     * @param void
     * @return null
     */
    function index() {
      if($this->active_filter->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_filter->canUse($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
    	list($assignments, $pagination) = AssignmentFilters::executeFilter($this->logged_user, $this->active_filter, null, (integer) $this->request->get('page'));
      
      $this->smarty->assign(array(
        'assignments' => $assignments,
        'pagination' => $pagination,
        'grouped_filters' => AssignmentFilters::findGrouped($this->logged_user),
      ));
    } // index
    
    /**
     * Render RSS feed for a spcific filter
     *
     * @param void
     * @return null
     */
    function rss() {
      if($this->active_filter->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_filter->canUse($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
    	require_once ANGIE_PATH . '/classes/feed/init.php';
    	
    	$feed = new Feed($this->owner_company->getName() . ' - ' . $this->active_filter->getName(), $this->active_filter->getUrl());
    	
    	$assignments = AssignmentFilters::executeFilter($this->logged_user, $this->active_filter, false);
      
      if(is_foreachable($assignments)) {
        $project_ids = array();
        foreach($assignments as $assignment) {
          if(!in_array($assignment->getProjectId(), $project_ids)) {
            $project_ids[] = $assignment->getProjectId();
          } // if
        } // foreach
        
        $projects = array();
        if(is_foreachable($project_ids)) {
          $rows = db_execute_all('SELECT id, name FROM ' . TABLE_PREFIX . 'projects WHERE id IN (?)', $project_ids);
          if(is_foreachable($rows)) {
            foreach($rows as $row) {
              $projects[$row['id']] = $row['name'];
            } // foreach
          } // if
        } // if
        
        foreach($assignments as $assignment) {
          $title = '['.array_var($projects, $assignment->getProjectId()).'] ' . $assignment->getVerboseType() . ' "' . $assignment->getName() . '"';
          
          $this->smarty->assign('_assignment', $assignment);
          
          $body = $this->smarty->fetch(get_template_path('_feed_body', 'assignment_filters', RESOURCES_MODULE));
          
          $item = new FeedItem($title, $assignment->getViewUrl(), $body, $assignment->getCreatedOn());
          $item->setId($assignment->getViewUrl());
          
          $feed->addItem($item);
        } // foreach
      } // if
      
      print render_rss_feed($feed);
      die();
    } // rss
    
    /**
     * Create new filter
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if(!AssignmentFilter::canAdd($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
    	$filter_data = $this->request->post('filter');
    	if(!is_array($filter_data)) {
    	  $filter_data = array(
    	    'user_filter' => USER_FILTER_LOGGED_USER,
    	    'objects_per_page' => 30,
    	  );
    	} // if
    	$this->smarty->assign('filter_data', $filter_data);
    	
    	if($this->request->isSubmitted()) {
    	  $this->active_filter = new AssignmentFilter();
    	  $this->active_filter->setAttributes($filter_data);
    	  
    	  $save = $this->active_filter->save();
    	  if($save && !is_error($save)) {
    	    flash_success("Filter ':name' has been created", array('name' => $this->active_filter->getName()));
    	    $this->redirectToUrl($this->active_filter->getUrl());
    	  } else {
    	    $this->smarty->assign('errors', $save);
    	  } // if
    	} // if
    } // add
    
    /**
     * Update an existing filter
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
    	if($this->active_filter->isNew()) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_filter->canEdit($this->logged_user)) {
    	  $this->httpError(HTTP_ERR_FORBIDDEN);
    	} // if
    	
    	$filter_data = $this->request->post('filter');
    	if(!is_array($filter_data)) {
    	  $filter_data = array(
    	    'name' => $this->active_filter->getName(),
    	    'group_name' => $this->active_filter->getGroupName(),
    	    'is_private' => $this->active_filter->getIsPrivate(),
    	    'user_filter' => $this->active_filter->getUserFilter(),
    	    'user_filter_data' => $this->active_filter->getUserFilterData(),
    	    'date_filter' => $this->active_filter->getDateFilter(),
    	    'date_from' => $this->active_filter->getDateFrom(),
    	    'date_to' => $this->active_filter->getDateTo(),
    	    'status_filter' => $this->active_filter->getStatusFilter(),
    	    'project_filter' => $this->active_filter->getProjectFilter(),
    	    'project_filter_data' => $this->active_filter->getProjectFilterData(),
    	    'order_by' => $this->active_filter->getOrderBy(),
    	    'objects_per_page' => $this->active_filter->getObjectsPerPage(),
    	  );
    	} // if
    	
    	$this->smarty->assign('filter_data', $filter_data);
    	
    	if($this->request->isSubmitted()) {
    	  $old_name = $this->active_filter->getName();
    	  
    	  $this->active_filter->setAttributes($filter_data);
    	  
    	  $save = $this->active_filter->save();
    	  if($save && !is_error($save)) {
    	    flash_success("Filter ':name' has been updated", array('name' => $old_name));
    	    $this->redirectToUrl($this->active_filter->getUrl());
    	  } else {
    	    $this->smarty->assign('errors', $save);
    	  } // if
    	} // if
    } // edit
    
    /**
     * Drop an existing filter
     *
     * @param void
     * @return null
     */
    function delete() {
    	if($this->active_filter->isNew()) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_filter->canEdit($this->logged_user)) {
    	  $this->httpError(HTTP_ERR_FORBIDDEN);
    	} // if
    	
    	if($this->request->isSubmitted()) {
    	  $delete = $this->active_filter->delete();
    	  if($delete && !is_error($delete)) {
    	    flash_success("Filter ':name' has been deleted", array('name' => $this->active_filter->getName()));
    	  } else {
    	    flash_error("Failed to delete ':name' filter", array('name' => $this->active_filter->getName()));
    	  } // if
    	  $this->redirectTo('assignments');
    	} // if
    } // delete
    
    /**
     * Utility method for generating additional controls for add / edit forms
     *
     * @param void
     * @return null
     */
    function partial_generator() {
    	$select_box = $this->request->get('select_box');
    	
    	// remove filter[...] around the value we need
    	$select_box = substr($select_box, 7, strlen($select_box) - 8);
    	$option_value = $this->request->get('option_value');
    	
    	switch($select_box) {
    	  case 'user_filter':
    	    if($option_value == 'company') {
    	      require_once SYSTEM_MODULE_PATH . '/helpers/function.select_company.php';
    	      print smarty_function_select_company(array('name' => 'filter[user_filter_data]'), $this->smarty);
    	    } elseif($option_value == USER_FILTER_SELECTED) {
    	      require_once SYSTEM_MODULE_PATH . '/helpers/function.select_users.php';
    	      print smarty_function_select_users(array('name' => 'filter[user_filter_data]'), $this->smarty);
    	    } // if
    	    break;
    	  case 'project_filter':
    	    if($option_value == 'selected') {
    	      require_once SYSTEM_MODULE_PATH . '/helpers/function.select_projects.php';
    	      print smarty_function_select_projects(array(
    	        'name' => 'filter[project_filter_data]', 
    	        'user' => $this->logged_user
    	      ), $this->smarty);
    	    } // if
    	    break;
    	  case 'date_filter':
    	    require_once SMARTY_PATH . '/plugins/function.select_date.php';
    	    if($option_value == 'selected_date') {
    	      print smarty_function_select_date(array('name' => 'filter[date_from]'), $this->smarty);
    	    } elseif($option_value == 'selected_range') {
    	      print '<table>
    	        <tr>
    	          <td>' . smarty_function_select_date(array('name' => 'filter[date_from]'), $this->smarty) . '</td>
    	          <td style="width: 10px; text-align: center;">-</td>
    	          <td>' . smarty_function_select_date(array('name' => 'filter[date_to]'), $this->smarty) . '</td>
    	        </tr>
    	      </table>';
    	    } // if
    	    break;
    	} // switch
      die();
    } // partial_generator
    
  }

?>