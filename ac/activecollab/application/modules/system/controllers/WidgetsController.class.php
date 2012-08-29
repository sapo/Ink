<?php

  /**
   * Implement methods used by various widgets, not directly by user
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class WidgetsController extends ApplicationController {
    
    /**
     * Construct widgets controller
     *
     * @param Request $request
     * @return WidgetsController
     */
    function __construct($request) {
    	parent::__construct($request);
    	
    	$this->skip_layout = true;
    } // __construct
    
    /**
     * Render content for select_users popup dialog
     *
     * @param void
     * @return null
     */
    function select_users() {
      $company_id = $this->request->getId('company_id');
      
      $company = null;
      if($company_id) {
        $company = Companies::findById($company_id);
      } // if
      
      $project_id = $this->request->getId('project_id');
      
      $project = null;
      if($project_id) {
        $project = Projects::findById($project_id);
      } // if
      
      $exclude_user_ids = $this->request->get('exclude_user_ids');
      if($exclude_user_ids) {
        $exclude_user_ids = explode(',', $exclude_user_ids);
      } // if
      
      $selected_user_ids = $this->request->get('selected_user_ids');
      if($selected_user_ids) {
        $selected_user_ids = explode(',', $selected_user_ids);
      } // if
      
      if(is_foreachable($exclude_user_ids) && is_foreachable($selected_user_ids)) {
        foreach($selected_user_ids as $k => $v) {
          if(in_array($v, $exclude_user_ids)) {
            unset($selected_user_ids[$k]);
          } // if
        } // foreach
      } // if
      
      if(is_foreachable($selected_user_ids)) {
        $selected_users = Users::findByIds($selected_user_ids);
      } else {
        $selected_users = null;
      } // if
      
      $grouped_users = Users::findForSelect($company, $project, $exclude_user_ids);
      
      $this->smarty->assign(array(
        'widget_id' => $this->request->get('widget_id'),
        'grouped_users' => $grouped_users,
        'selected_users' => $selected_users,
        'selected_users_cycle_name' => $this->request->get('widget_id') . '_select_users',
      ));
    } // select_users
    
    /**
     * Show select projects popup
     *
     * @param void
     * @return null
     */
    function select_projects() {
      $exclude_ids = $this->request->get('exclude_ids');
      
      $selected_project_ids = $this->request->get('selected_ids');
      if(is_foreachable($exclude_ids) && is_foreachable($selected_ids)) {
        foreach($selected_project_ids as $k => $v) {
          if(in_array($v, $exclude_ids)) {
            unset($selected_project_ids[$k]);
          } // if
        } // foreach
      } // if
      
      $statuses = $this->request->get('active_only') ? array(PROJECT_STATUS_ACTIVE) : null;
      
      $this->smarty->assign(array(
        'widget_id' => $this->request->get('widget_id'),
        'projects' => Projects::findNamesByUser($this->logged_user, $statuses, $exclude_ids, (boolean) $this->request->get('show_all')),
        'selected_project_ids' => $selected_project_ids,
      ));
    } // select_projects
    
    /**
     * Render jump to projects page
     *
     * @param void
     * @return null
     */
    function jump_to_project() {
      $pinned_projects = null;
      $active_projects = Projects::findNamesByUser($this->logged_user, PROJECT_STATUS_ACTIVE);
      
      if(is_foreachable($active_projects)) {
        $pinned_project_ids = PinnedProjects::findProjectIdsByUser($this->logged_user);
        
        if(is_foreachable($pinned_project_ids)) {
          $pinned_projects = array();
          foreach($pinned_project_ids as $id) {
            if(isset($active_projects[$id])) {
              $pinned_projects[$id] = $active_projects[$id];
              unset($active_projects[$id]);
            } // if
          } // if
        } // if
      } // if
      
      $this->smarty->assign(array(
        'active_projects' => $active_projects,
        'pinned_projects' => $pinned_projects,
      ));
    } // jump_to_project
    
    /**
     * Show and process object subscribers page
     *
     * @param void
     * @return null
     */
    function object_subscribers() {
      $this->skip_layout = true;
      
      $object = null;
      
      $object_id = $this->request->getId('object_id');
      if($object_id) {
        $object = ProjectObjects::findById($object_id);
      } // if
      
      if(!instance_of($object, 'ProjectObject')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      $project = $object->getProject();
      if(!instance_of($project, 'Project')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$object->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $this->smarty->assign(array(
        'active_project' => $project,
        'active_object' => $object,
      ));
      
      $users = $project->getUsers();
      if(is_foreachable($users)) {
        $people = array();
        $grouped_users = array();
        
        foreach($users as $user) {
          $company_id = $user->getCompanyId();
          if(!isset($people[$company_id])) {
            $people[$company_id] = array(
              'users' => null,
              'company' => null,
            );
          } // if
          $people[$company_id]['users'][] = $user;
        } // foreach
        
        $companies = Companies::findByIds(array_keys($people));
        foreach($companies as $company) {
          $people[$company->getId()]['company'] = $company;
        } // foreach
        
        $this->smarty->assign('people', $people);
      } else {
        $this->smarty->assign('people', null);
      } // if
    } // object_subscribers
    
    
    /**
     * Provides logic for image picker dialog
     * 
     * @param void
     * @return null
     */
    function image_picker() {
      $project_id = $this->request->get('project_id');
      if($project_id) {
        $this->active_project = Projects::findById($project_id);
      } // if
      
      if (!instance_of($this->active_project, 'Project')) {
        $this->active_project = new Project();
      } // if
      
      $image_picker_url = assemble_url('image_picker', array('project_id' => $this->active_project->getId()));
      
      $this->smarty->assign(array(
        'image_picker_url' => $image_picker_url,
        'disable_upload' => (boolean) $this->request->get('disable_upload'),
      ));
      
      if ($this->request->isSubmitted()) {
        $action = $this->request->post('widget_action');
        
        switch ($action) {
        	case 'upload':
        	  // check if any file is uploaded
        	  $uploaded_file = array_var($_FILES, 'image', null);
        	  if (!is_array($uploaded_file)) {
        	    $this->httpError(HTTP_ERR_OPERATION_FAILED, lang('You did not uploaded any file'), true, true);
        	  } // if
        	  
        	  // we are setting base attributes
        		$attachment = new Attachment();
            $attachment->setName($uploaded_file['name']);
            $attachment->setMimeType($uploaded_file['type']);
            $attachment->setSize($uploaded_file['size']);
            $attachment->setAttachmentType(ATTACHMENT_TYPE_ATTACHMENT);
            $attachment->setCreatedBy($this->logged_user);
            $attachment->setCreatedOn(new DateTimeValue());
        	  
            // check if uploaded file is image
        	  if (!$attachment->isImage()) {
        	    $this->httpError(HTTP_ERR_OPERATION_FAILED, lang('Uploaded file is not image'), true, true);
        	  } // if
        	  
            $destination_file = get_available_uploads_filename();          		
            if (!move_uploaded_file($uploaded_file['tmp_name'], $destination_file)) {
              $this->httpError(HTTP_ERR_OPERATION_FAILED, lang('Could not copy uploaded image to work folder'), true, true);
            } // if
            
            $attachment->setLocation(basename($destination_file));
        		$save = $attachment->save();
        		if (!$save || is_error($save)) {
        		  @unlink($destination_file);
        		  $this->httpError(HTTP_ERR_OPERATION_FAILED, $save->getMessage(), true, true);
        		} // if
        		
        		echo "<img attachment_id='". $attachment->getId() ."' src='". $attachment->getViewUrl($this->active_project->getId()) ."' />";
      		  die();
        		break;
        
        	default:
        		break;
        } // switch
      } // if
    } // image_picker
    
  }

?>