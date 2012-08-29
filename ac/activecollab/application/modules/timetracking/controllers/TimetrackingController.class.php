<?php

  // Extends project controller
  use_controller('project', SYSTEM_MODULE);
  
  /**
   * Timetracking controller
   *
   * @package activeCollab.modules.timetracking
   * @subpackage controllers
   */
  class TimetrackingController extends ProjectController {
  
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'timetracking';
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = TIMETRACKING_MODULE;
  
    /**
     * Selected time
     *
     * @var TimeRecord
     */
    var $active_time;
    
    /**
     * Selected project object
     *
     * @var ProjectObject
     */
    var $active_object;
    
    /**
     * Actions that are available through API
     *
     * @var array
     */
    var $api_actions = array('index', 'add', 'view', 'edit', 'reports');
  
    /**
     * Construct timetracking controller
     *
     * @param Request $request
     * @return TimetrackingController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if($this->logged_user->getProjectPermission('timerecord', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if

      $time_url = timetracking_module_url($this->active_project);
      $time_add_url = timetracking_module_add_record_url($this->active_project);
      
      $this->wireframe->addBreadCrumb(lang('Time'), $time_url);
      
      if($this->logged_user->isAdministrator() || $this->logged_user->getSystemPermission('use_time_reports')) {
        $this->wireframe->addPageAction(lang('Reports'), timetracking_module_reports_url($this->active_project), null, array('id' => 'timetracking_reports'));
      } // if
      
      $time_id = $this->request->getId('time_id');
      if($time_id) {
        $this->active_time = TimeRecords::findById($time_id);
      } // if
      
      if(!instance_of($this->active_time, 'TimeRecord')) {
        $this->active_time = new TimeRecord();
      } // if
      
      $object_id = $this->request->getId('for');
      if($object_id) {
        $this->active_object = ProjectObjects::findById($object_id);
      } // if
      
      if(instance_of($this->active_object, 'ProjectObject')) {
        $time_url = timetracking_module_url($this->active_project, $this->active_object);
        $time_add_url = timetracking_module_add_record_url($this->active_project, array('for' => $this->active_object->getId()));
        
        $this->wireframe->addBreadCrumb($this->active_object->getName(), $time_url);
      } // if
      
      $this->smarty->assign(array(
        'active_time'   => $this->active_time,
        'active_object' => $this->active_object,
        'time_url'      => $time_url,
        'add_url'       => $time_add_url,
        'page_tab'      => 'time',
        'can_manage'    => $this->logged_user->getProjectPermission('timerecord', $this->active_project) >= PROJECT_PERMISSION_MANAGE,
      ));
    } // __construct
  
    /**
     * Show timetracking module homepage
     *
     * @param void
     * @return null
     */
    function index() {
      if($this->request->isApiCall()) {
        $this->serveData(TimeRecords::findByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility()), 'time_records');
      } else {
        
        // Content for widget popup
        if($this->request->get('for_popup_dialog')) {
          $this->_render_popup_content();
          
        // Classic page
        } else {
          if(instance_of($this->active_object, 'ProjectObject')) {
            $this->wireframe->addPageMessage(lang('Time spent on <a href=":url">:name</a> :type', array(
              'url' => $this->active_object->getViewUrl(),
              'name' => $this->active_object->getName(),
              'type' => $this->active_object->getVerboseType(true),
            )), 'info');
          } // if
          
          $timetracking_data = array(
            'record_date' => new DateValue(time() + get_user_gmt_offset($this->logged_user)),
            'user_id' => $this->logged_user->getId(),
          );
          
          $per_page = 20;
          $page = (integer) $this->request->get('page');
          if($page < 1){
            $page = 1;
          } // if
          
          if(instance_of($this->active_object, 'ProjectObject')) {
            list($timerecords, $pagination) = TimeRecords::paginateByObject($this->active_object, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
          } else {
            list($timerecords, $pagination) = TimeRecords::paginateByProject($this->active_project, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
          } // if
          
          // Mark this objects as read
          if(is_foreachable($timerecords)) {
            foreach($timerecords as $timerecord) {
              ProjectObjectViews::log($timerecord, $this->logged_user);
            } // foreach
          } // if
          
          $this->smarty->assign(array(
            'timetracking_data' => $timetracking_data,
            'timerecords'       => $timerecords,
            'pagination'        => $pagination,
            'can_add'           => TimeRecord::canAdd($this->logged_user, $this->active_project),
          ));
          
          js_assign('mass_update_url', assemble_url('project_time_mass_update', array('project_id' => $this->active_project->getId())));
        } // if
      } // if
    } // index
    
    /**
     * Show single record information (API only)
     *
     * @param void
     * @return null
     */
    function view() {
    	if($this->request->isApiCall()) {
    	  if($this->active_time->isNew()) {
    	    $this->httpError(HTTP_ERR_NOT_FOUND, null, true, true);
    	  } // if
    	  
    	  if($this->active_time->canView($this->logged_user)) {
    	    $this->serveData($this->active_time, 'time_record');
    	  } else {
    	    $this->httpError(HTTP_ERR_FORBIDDEN, null, true, true);
    	  } // if
    	} else {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    } // view
    
    /**
     * Update multiple time records
     *
     * @param void
     * @return null
     */
    function mass_update() {
      if($this->request->isSubmitted()) {
        $updated = 0; // number of successfully update records
        $message = 'No records has been updatede or moved to trash';
        
        $action = $this->request->post('action');
        $time_record_ids = $this->request->post('time_record_ids');
        if(is_foreachable($time_record_ids)) {
          $time_records = TimeRecords::findByIds($time_record_ids, STATE_VISIBLE, $this->logged_user->getVisibility());
          if(is_foreachable($time_records)) {
            db_begin_work();
            
            switch($action) {
              
              // Mark as billable
              case 'mark_as_billable':
                $message = ':count records marked as billable';
                
                foreach($time_records as $time_record) {
                  if($time_record->canChangeBillableStatus($this->logged_user)) {
                    $time_record->setBillableStatus(BILLABLE_STATUS_BILLABLE);
                    $save = $time_record->save();
                    if($save && !is_error($save)) {
                      $updated++;
                    } // if
                  } // if
                } // foreach
                
                break;
                
              // Mark as non-billable
              case 'mark_as_not_billable':
                $message = ':count records marked as non-billable';
                
                foreach($time_records as $time_record) {
                  if($time_record->canChangeBillableStatus($this->logged_user)) {
                    $time_record->setBillableStatus(BILLABLE_STATUS_NOT_BILLABLE);
                    $save = $time_record->save();
                    if($save && !is_error($save)) {
                      $updated++;
                    } // if
                  } // if
                } // foreach
                
                break;
              
              // Mark as billed
              case 'mark_as_billed':
                $message = ':count records marked as billed';
                
                foreach($time_records as $time_record) {
                  if($time_record->canChangeBillableStatus($this->logged_user)) {
                    $time_record->setBillableStatus(BILLABLE_STATUS_BILLED);
                    $save = $time_record->save();
                    if($save && !is_error($save)) {
                      $updated++;
                    } // if
                  } // if
                } // foreach
                
                break;
                
              // Mark as not billed
              case 'mark_as_not_billed':
                $message = ':count records marked as not billed';
                
                foreach($time_records as $time_record) {
                  if($time_record->canChangeBillableStatus($this->logged_user)) {
                    $time_record->setBillableStatus(BILLABLE_STATUS_BILLABLE);
                    $save = $time_record->save();
                    if($save && !is_error($save)) {
                      $updated++;
                    } // if
                  } // if
                } // foreach
                
                break;
                
              // Move to trash
              case 'move_to_trash':
                $message = ':count records moved to trash';
                
                foreach($time_records as $time_record) {
                  if($time_record->canDelete($this->logged_user)) {
                    $trash = $time_record->moveToTrash();
                    if($trash && !is_error($trash)) {
                      $updated++;
                    } // if
                  } // if
                } // foreach
                
                break;
            } // switch
            
            db_commit();
          } // if
        } // if
        
        flash_success($message, array('count' => $updated));
        $this->redirectToReferer(timetracking_module_url($this->active_project));
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // mass_update
    
    /**
     * Create a new time record
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      if(!TimeRecord::canAdd($this->logged_user, $this->active_project)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $timetracking_data = $this->request->post('time');
      $this->smarty->assign(array(
        'timetracking_data' => $timetracking_data,
      ));
      
      $render_row = (boolean) $this->request->get('async');
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $timetracking_data['value'] = time_to_float($timetracking_data['value']);
        
        $this->active_time->log_activities = false;
        $this->active_time->setAttributes($timetracking_data);
        $this->active_time->setProjectId($this->active_project->getId());
        $this->active_time->setCreatedBy($this->logged_user);
        
        $user_id = array_var($timetracking_data, 'user_id');
        if($user_id) {
          $user = Users::findById($user_id);
          if(instance_of($user, 'User')) {
            $this->active_time->setUser($user);
          } // if
        } // if
        
        $this->active_time->setState(STATE_VISIBLE);
        
        if(instance_of($this->active_object, 'ProjectObject')) {
          $this->active_time->setParent($this->active_object);
          $this->active_time->setVisibility($this->active_object->getVisibility());
        } else {
          $this->active_time->setVisibility(VISIBILITY_NORMAL);
        } // if
        
        $save = $this->active_time->save();
  
        if($save && !is_error($save)) {
          $activity_log = new TimeAddedActivityLog();
          $activity_log->log($this->active_time, $this->logged_user);
          
          db_commit();
          $this->active_time->ready();
          
          if($this->request->get('for_popup_dialog')) {
            $this->_render_popup_content();
          } // if
          
          if($render_row) {
            $this->smarty->assign('timerecord', $this->active_time);
            $this->smarty->display(get_template_path('_time_row', 'timetracking', TIMETRACKING_MODULE));
            die();
          } else {
            if($this->request->getFormat() == FORMAT_HTML) {
              if(!is_null($render_row)) {
                flash_success('Time record has been added');
                $this->redirectToUrl(timetracking_module_url($this->active_project));          	
              } // if
            } else {
              $this->serveData($this->active_time, 'time_record');
            } // if
          } // if
        } else {
          db_rollback();
          
          if($this->request->get('for_popup_dialog')) {
            $this->_render_popup_content();
          } // if
          
          if($render_row) {
            $this->httpError(HTTP_ERR_INVALID_PROPERTIES);
          } else {
            if($this->request->getFormat() == FORMAT_HTML) {
              $this->smarty->assign('errors', $save);
            } else {
              $this->serveData($save);
            } // if
          } // if
        } // if
      } // if
    } // add
    
    /**
     * Quick add time record
     *
     * @param void
     * @return null
     */
    function quick_add() {     
      if(!TimeRecord::canAdd($this->logged_user, $this->active_project)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN, lang("You don't have permission for this action"), true, true);
      } // if
      
      $this->skip_layout = true;
      
      $time_record_data = $this->request->post('time_record');
      if (!is_array($time_record_data)) {
        $time_record_data = array(
          'record_date' => new DateValue(time() + get_user_gmt_offset($this->logged_user)),
          'billable_status' => BILLABLE_STATUS_BILLABLE,
        );
      } // if
      $this->smarty->assign(array(
        'time_record_data' => $time_record_data,
        'quick_add_url' => assemble_url('project_time_quick_add', array('project_id' => $this->active_project->getId())),
      ));
      
      if ($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_time = new TimeRecord();
        
        $time_record_data['value'] = time_to_float($time_record_data['value']);
        
        $this->active_time->setAttributes($time_record_data);
        $this->active_time->setProjectId($this->active_project->getId());
        $this->active_time->setCreatedBy($this->logged_user);
        $this->active_time->setState(STATE_VISIBLE);
        $this->active_time->setVisibility(VISIBILITY_NORMAL);
        
        $user_id = array_var($time_record_data, 'user_id');
        if($user_id) {
          $user = Users::findById($user_id);
          if(instance_of($user, 'User')) {
            $this->active_time->setUser($user);
          } // if
        } // if
        
        $save = $this->active_time->save();
        if($save && !is_error($save)) {
          db_commit();
          $this->active_time->ready();
          
          $this->smarty->assign(array(
            'active_time_record' => $this->active_time,
            'time_record_data' => array(
                'user_id'         => $this->logged_user->getId(),
                'record_date'     => $this->active_time->getRecordDate(),
                'billable_status' => $this->active_time->getBillableStatus(),
              ),
            'selected_form_url' => assemble_url('project_time_quick_add', array('project_id' => $this->active_project->getId(), 'skip_layout' => true)),
            'project_id' => $this->active_project->getId()
          ));
          $this->skip_layout = true;
        } else {
          db_rollback();
          $this->httpError(HTTP_ERR_OPERATION_FAILED, $save->getErrorsAsString(), true, true);
        } // if
      } // if
    } // quick_add
    
    /**
     * Upate time record
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      if($this->active_time->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_time->canEdit($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $timetracking_data = $this->request->post('time');
      if(!is_array($timetracking_data)) {
        $timetracking_data = array(
          'user_id'         => $this->active_time->getUserId(),
          'record_user'     => $this->active_time->getUser(),
          'value'           => $this->active_time->getValue(),
          'body'            => $this->active_time->getBody(),
          'record_date'     => $this->active_time->getRecordDate(),
          'billable_status' => $this->active_time->getBillableStatus()
        );
      } // if
      
      $this->smarty->assign('timetracking_data', $timetracking_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        $timetracking_data['value'] = time_to_float($timetracking_data['value']);
        
        $old_user_id = $this->active_time->getUserId();
        
        $this->active_time->setAttributes($timetracking_data);
        
        if(isset($timetracking_data['user_id']) && $timetracking_data['user_id']) {
          $user_id = array_var($timetracking_data, 'user_id');
          
          if($user_id) {
            $user = Users::findById($user_id);
            if(instance_of($user, 'User')) {
              $timetracking_data['record_user'] = $user;
              if($user_id != $old_user_id) {
                $this->active_time->setUser($user);
              } // if
            } // if
          } // if
        } else {
          if($user_id == $old_user_id) {
            $timetracking_data['record_user'] = $this->active_time->getUser(); // Not changed anonymous user
          } // if
        } // if
        
        $this->smarty->assign('timetracking_data', $timetracking_data);
        
        $save = $this->active_time->save();
        if($save && !is_error($save)) {
          db_commit();
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('Time record #:record_id has been updated', array('record_id' => $this->active_time->getId()));
            $this->redirectToUrl($this->smarty->get_template_vars('time_url'));
          } else {
            $this->serveData($this->active_time, 'time_record');
          } // if
        } else {
          db_rollback();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            $this->smarty->assign('errors', $save);
          } else {
            $this->serveData($save);
          } // if
        } // if
      } // if
    } // edit
    
    /**
     * Update time record billed state
     *
     * @param void
     * @return null
     */
    function update_billed_state() {
      if($this->active_time->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_time->canChangeBillableStatus($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        $to = $this->request->get('to') ? BILLABLE_STATUS_BILLED : BILLABLE_STATUS_BILLABLE;
        
        $this->active_time->setBillableStatus($to);
        $this->active_time->save();
        
        if($this->active_time->isBilled()) {
          print open_html_tag('a', array(
            'href' => $this->active_time->getUpdateBilledStateUrl(false),
            'class' => 'mark_time_record_as_billed',
            'title' => lang('Billed ...'),
          )) . '<img src="' . get_image_url('dollar-small.gif') . '" alt="" /></a>';
        } else {
          print open_html_tag('a', array(
            'href' => $this->active_time->getUpdateBilledStateUrl(true),
            'class' => 'mark_time_record_as_billed',
            'title' => lang('Not billed ...'),
          )) . '<img src="' . get_image_url('gray-dollar-small.gif') . '" alt="" /></a>';
        } // if
        
        die();
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      sleep(3);
      print 'tup';
      die();
    } // update_billed_state
    
    /**
     * Render popup content
     *
     * @param void
     * @return null
     */
    function _render_popup_content() {
      if(!instance_of($this->active_object, 'ProjectObject')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(TimeRecord::canAdd($this->logged_user, $this->active_project)) {
        $add_record_url = timetracking_module_add_record_url($this->active_project, array(
          'for' => $this->active_object->getId(),
          'for_popup_dialog' => 1
        ));
      } else {
        $add_record_url = false;
      } // if
      
      $object_time = TimeRecords::sumObjectTime($this->active_object);
      $tasks_time = $this->active_object->can_have_tasks ? TimeRecords::sumTasksTime($this->active_object) : 0;
      
      $this->smarty->assign(array(
        'selected_user' => $this->logged_user,
        'selected_date' => new DateValue(time() + get_user_gmt_offset($this->logged_user)),
        'selected_billable_status' => BILLABLE_STATUS_BILLABLE,
        'object_time' => float_format($object_time, 2),
        'tasks_time' => float_format($tasks_time, 2),
        'total_time' => float_format($object_time + $tasks_time, 2),
        'add_url' => $add_record_url,
      ));
      
      $this->smarty->display(get_template_path('_popup', null, TIMETRACKING_MODULE));
      die();
    } // _render_popup_content
    
    /**
     * Exports time records
     * 
     * @param void
     * @return null
     */
    function export() {
      $object_visibility = array_var($_GET, 'visibility', VISIBILITY_NORMAL);
      $exportable_modules = explode(',', array_var($_GET,'modules', null));
      if (!is_foreachable($exportable_modules)) {
        $exportable_modules = null;
      } // if
      
      require_once(PROJECT_EXPORTER_MODULE_PATH.'/models/ProjectExporterOutputBuilder.class.php');
      $output_builder = new ProjectExporterOutputBuilder($this->active_project, $this->smarty, $this->active_module, $exportable_modules);
      if (!$output_builder->createOutputFolder()) {
        $this->serveData($output_builder->execution_log, 'execution_log', null, FORMAT_JSON);
      } // if
      $output_builder->createAttachmentsFolder();
      
      $timerecords = TimeRecords::findByProject($this->active_project, STATE_VISIBLE, $object_visibility);
      
      $distinct_months = array();
      foreach ($timerecords as $timerecord) {
      	$date = $timerecord->getRecordDate();
      	$exists = false;
      	for ($x=0; $x<count($distinct_months); $x++) {
      	  if ($distinct_months[$x]['month']==$date->getMonth() && $distinct_months[$x]['year']==$date->getYear()) {
      	    $exists = true;
      	  } // if
      	} // for
      	
      	if (!$exists) {
      	  $distinct_months[] = array(
      	   "year"                => $date->getYear(),
      	   "month"               => $date->getMonth(),
      	   "month_string"        => $date->date_data['month'],
           "beginning_of_month"  => DateTimeValue::beginningOfMonth($date->getMonth(), $date->getYear()),
           "end_of_month"        => DateTimeValue::endOfMonth($date->getMonth(), $date->getYear()),
      	  );
      	} // if
      } // foreach
      
      $people = ProjectUsers::findUsersByProject($this->active_project);
      $companies = Companies::findByProject($this->active_project);
      
      $total_times = array();
      foreach ($people as $person) {
        $person->temp_total_time = TimeRecords::getTotalUserTimeOnProject($this->active_project, $person);
      } // foreach

      $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'index');
      $output_builder->smarty->assign(array(
        "distinct_months" => $distinct_months,
        "people"          => $people,
        "companies"       => $companies,
        "total_times"     => $total_times,
        "timerecords"     => $timerecords,
      ));
      $output_builder->outputToFile('index');
                 
      
      // export monthly report
      if (is_foreachable($distinct_months)) {
        $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'monthly');
        foreach ($distinct_months as $distinct_month) {
          $output_builder->smarty->assign(array(
            'current_month' => $distinct_month,
            'start_date'    => DateTimeValue::beginningOfMonth($distinct_month[month], $distinct_month['year']),
            'end_date'      => DateTimeValue::endOfMonth($distinct_month['month'], $distinct_month['year']),
          ));
          $output_builder->outputToFile('monthly_'.$distinct_month['month'].'_'.$distinct_month['year']);
        } // foreach
      } // if
      
      // export report for persons
      if (is_foreachable($people)) {
        $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'person');
        foreach ($people as $person) {
          $output_builder->smarty->assign(array(
            'current_person' => $person
          ));
          $output_builder->outputToFile('user_'.$person->getId());
        } // foreach
      } // if
      
      $this->serveData($output_builder->execution_log, 'execution_log', null, FORMAT_JSON);
    } // export
    
  }
  
?>
