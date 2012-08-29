<?php

  // We need project controller
  use_controller('project', SYSTEM_MODULE);
  
  /**
   * Files controller
   *
   * @package activeCollab.modules.files
   * @subpackage controllers
   */
  class FilesController extends ProjectController {
  
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = FILES_MODULE;
    
    /**
     * Selected checklist
     *
     * @var File
     */
    var $active_file;
    
    /**
     * Enable categories support for this controller
     *
     * @var boolean
     */
    var $enable_categories = true;
    
    /**
     * Actions available through API
     *
     * @var array
     */
    var $api_actions = array('index', 'view', 'upload_single', 'edit');
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return FilesController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if($this->logged_user->getProjectPermission('file', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $files_url = files_module_url($this->active_project);
      $attachments_url = files_module_url($this->active_project, array('show_attachments' => true));
      
      $this->wireframe->addBreadCrumb(lang('Files'), $files_url);
      
      $upload_url = false;
      if(File::canAdd($this->logged_user, $this->active_project)) {
        if($this->active_category->isLoaded()) {
          $upload_url = files_module_upload_url($this->active_project, array(
            'category_id' => $this->active_category->getId(),
          ));
        } else {
          $upload_url = files_module_upload_url($this->active_project);
        } // if
        
        $this->wireframe->addPageAction(lang('Upload Files'), $upload_url);
      } // if
      
      $file_id = $this->request->getId('file_id');
      if($file_id) {
        $this->active_file = ProjectObjects::findById($file_id);
      } // if
      
      if(instance_of($this->active_file, 'File')) {
        $this->wireframe->addBreadCrumb($this->active_file->getName(), $this->active_file->getViewUrl());
      } else {
        $this->active_file = new File();
      } // if
      
      $this->smarty->assign(array(
        'active_file'     => $this->active_file,
        'files_url'       => $files_url,
        'attachments_url' => $attachments_url,
        'upload_url'      => $upload_url,
        'page_tab'        => 'files',
      ));
    } // __construct
    
    /**
     * Show files index page
     *
     * @param void
     * @return null
     */
    function index() {
      if($this->request->isApiCall()) {
        $this->serveData(Files::findByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getVisibility()), 'files');
      } else {
        $per_page = 10;
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        $show_attachments = $this->request->get('show_attachments');
        if($this->active_category->isLoaded()) {
          $this->wireframe->addBreadCrumb(clean($this->active_category->getName()), $this->active_category->getViewUrl());
          list($files, $pagination) = Files::paginateByCategory($this->active_category, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
        } else {
          if ($show_attachments) {
            $this->wireframe->addBreadCrumb(lang('All Attachments'), files_module_url($this->active_project, array('show_attachments' => true)));
            $this->setTemplate('attachments');
            list($files, $pagination) = Attachments::paginateByProject($this->active_project, $this->logged_user, $page, $per_page);
          } else {
            $this->wireframe->addBreadCrumb(lang('All Files'), files_module_url($this->active_project, array('show_attachments' => true)));
            list($files, $pagination) = Files::paginateByProject($this->active_project, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
          } // if
        } // if
        
        $this->smarty->assign(array(
          'categories' => Categories::findByModuleSection($this->active_project, FILES_MODULE, 'files'),
          'files' => $files,
          'pagination' => $pagination,
          'attachments_view' => $show_attachments ? true : false,
          'mass_edit_url' => assemble_url('project_files_mass_edit', array('project_id' => $this->active_project->getId())), 
          'can_manage_categories' => $this->logged_user->isProjectLeader($this->active_project) || $this->logged_user->isProjectManager(),
        ));
      } // if
    } // index
    
    /**
     * Override view category page
     *
     * @param void
     * @return null
     */
    function view_category() {
      $this->redirectTo('project_files', array(
        'project_id' => $this->active_project->getId(),
        'category_id' => $this->request->getId('category_id')
      ));
    } // view_category
    
    /**
     * Show file details
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_file->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_file->canView($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $last_revision = $this->active_file->getLastRevision();
      if(!instance_of($last_revision, 'Attachment')) {
        flash_error('Invalid file - last revision was not found');
        $this->redirectToUrl(files_module_url($this->active_project));
      } // if
      
      ProjectObjectViews::log($this->active_file, $this->logged_user);
      
      if($this->request->isApiCall()) {
        $this->serveData($this->active_file, 'file', array('describe_revisions' => true));
      } else {
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        list($comments, $pagination) = $this->active_file->paginateComments($page, $this->active_file->comments_per_page, $this->logged_user->getVisibility());
        
        $this->smarty->assign(array(
          'revisions' => $this->active_file->getRevisions(),
          'last_revision' => $last_revision,
          'comments' => $comments,
          'pagination' => $pagination,
          'counter' => ($page - 1) * $this->active_file->comments_per_page,
        ));
      } // if
    } // view
    
    /**
     * Quick add file
     *
     * @param void
     * @return null
     */
    function quick_add() {
      if(!File::canAdd($this->logged_user, $this->active_project)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN, lang("You don't have permission for this action"), true, true);
      } // if
      
      $this->skip_layout = true;
            
      $file_data = $this->request->post('file');
      if (!is_array($file_data)) {
        $file_data = array(
          'visibility' => $this->active_project->getDefaultVisibility()
        );
      } // if
      $this->smarty->assign(array(
        'file_data' => $file_data,
        'form_urls' => $form_urls,
        'quick_add_url' => assemble_url('project_files_quick_add', array('project_id' => $this->active_project->getId())),
      ));
      
      if($this->request->isSubmitted()) {
        db_begin_work();
          
        $this->active_file = new File();
        $attached = attach_from_files($this->active_file, $this->logged_user);
        
        // Do we have an upload error?
        if(is_error($attached) || ($attached != 1)) {
          $this->httpError(HTTP_ERR_OPERATION_FAILED, lang("You haven't selected any file for upload"), true, true);
        } // if
        
        $this->active_file->setAttributes($file_data);
        $this->active_file->setBody(clean(array_var($file_data, 'body', null)));
        if($this->active_file->getName() == '') {
          $this->active_file->setName($this->active_file->pending_files[0]['name']);
        } // if
        $this->active_file->setRevision(1);
        $this->active_file->setProjectId($this->active_project->getId());
        $this->active_file->setCreatedBy($this->logged_user);
        $this->active_file->setState(STATE_VISIBLE);
        //$this->active_file->setVisibility(VISIBILITY_NORMAL);
        
        $save = $this->active_file->save();
        if($save && !is_error($save)) {
          $subscribers = array($this->logged_user->getId());
          if(is_foreachable($this->request->post('notify_users'))) {
            $subscribers = array_merge($subscribers, $this->request->post('notify_users'));
          } else {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          
          if(!in_array($this->active_project->getLeaderId(), $subscribers)) {
            $subscribers[] = $this->active_project->getLeaderId();
          } // if
          
          Subscriptions::subscribeUsers($subscribers, $this->active_file);
          
          db_commit();
          $this->active_file->ready();
          
          $this->smarty->assign(array(
            'active_file' => $this->active_file,
            'file_data' => array('visibility' => $this->active_project->getDefaultVisibility()),
            'project_id' => $this->active_project->getId()
          ));
        } else {
          db_rollback();
          $this->httpError(HTTP_ERR_OPERATION_FAILED, $save->getErrorsAsString(), true, true);
        } // if
      } // if
    } // quick_add
    
    /**
     * Edit existing file information
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
      
      if($this->active_file->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_file->canEdit($this->logged_user)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $file_data = $this->request->post('file');
      if(!is_array($file_data)) {
        $file_data = array(
          'name' => $this->active_file->getName(),
          'body' => $this->active_file->getBody(),
          'visibility' => $this->active_file->getVisibility(),
          'parent_id' => $this->active_file->getParentId(),
          'milestone_id' => $this->active_file->getMilestoneId(),
          'tags' => $this->active_file->getTags(),
        );
      } // if
      
      $this->smarty->assign('file_data', $file_data);
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $old_name = $this->active_file->getName();
        
        $this->active_file->setAttributes($file_data);
        
        $save = $this->active_file->save();
        if($save && !is_error($save)) {
          db_commit();
          
          if($this->request->isApiCall()) {
            $this->serveData($this->active_file, 'file');
          } else {
            flash_success('File ":name" has been updated', array('name' => $old_name));
            $this->redirectToUrl($this->active_file->getViewUrl());
          } // if
        } else {
          db_rollback();
          
          if($this->request->isApiCall()) {
            $this->serveData($save);
          } else {
            $this->smarty->assign('errors', $save);
          } // if
        } // if
      } // if
    } // edit
    
    /**
     * Upload new file version
     *
     * @param void
     * @return null
     */
    function new_version() {
    	if($this->active_file->isNew()) {
    	  $this->httpError(HTTP_ERR_NOT_FOUND);
    	} // if
    	
    	if(!$this->active_file->canEdit($this->logged_user)) {
    	  $this->httpError(HTTP_ERR_FORBIDDEN);
    	} // if
    	
    	if($this->request->isSubmitted()) {
    	  $attached = attach_from_files($this->active_file, $this->logged_user);
    	  
    	  if($attached && !is_error($attached)) {
    	    $this->active_file->setRevision($this->active_file->getRevision() + 1);
    	    
    	    $save = $this->active_file->save();
    	    if($save && !is_error($save)) {
    	      $last_revision = $this->active_file->getLastRevision();
            if(instance_of($last_revision, 'Attachment')) {
              $last_revision->setCreatedBy($this->logged_user);
              $last_revision->setAttachmentType(ATTACHMENT_TYPE_FILE_REVISION);
              $last_revision->save();
              
              event_trigger('on_new_revision', array(&$this->active_file, &$last_revision, &$this->logged_user));
              
              $activity_log = new NewFileVersionActivityLog();
              $activity_log->log($this->active_file, $this->logged_user, $last_revision->getId());
            } // if
            
            Subscriptions::subscribe($this->logged_user, $this->active_file);
            
            db_commit();
            
            flash_success('File ":name" has been updated', array('name' => $this->active_file->getName()));
            $this->redirectToUrl($this->active_file->getViewUrl());
    	    } else {
    	      db_rollback();
    	      $this->smarty->assign('errors', $save);
    	    } // if
    	  } else {
    	    if(is_error($attached)) {
    	      $errors = new ValidationErrors(array(
    	        'file' => $attached->getMessage(),
    	      ));
    	    } else {
    	      $errors = new ValidationErrors(array(
    	        'file' => lang('File not uploaded')
    	      ));
    	    } // if
    	    
    	    $this->smarty->assign('errors', $errors);
    	  } // if
    	} // if
    } // new_version
    
    /**
     * Export files
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
      
      $module_categories = Categories::findByModuleSection($this->active_project,$this->active_module,$this->active_module);
      $module_objects = Files::findByProject($this->active_project, STATE_VISIBLE, $object_visibility);

      $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'index');
      $output_builder->smarty->assign('categories',$module_categories);
      $output_builder->smarty->assign('objects', $module_objects);
      $output_builder->outputToFile('index');
            
      // export files by categories
      if (is_foreachable($module_categories)) {
        foreach ($module_categories as $module_category) {
          if (instance_of($module_category,'Category')) {
            $objects = Files::find(array(
              'conditions' => array('parent_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?',$module_category->getId() ,$this->active_project->getId(), 'File', STATE_VISIBLE, $object_visibility),
              'order'      => 'boolean_field_1, datetime_field_1 DESC',
            ));
            $output_builder->smarty->assign(array(
              'current_category' => $module_category,
              'objects' => $objects,
            ));
            $output_builder->outputToFile('category_'.$module_category->getId());
          } // if
        } // foreach
      } // if
            
      // export files
      if (is_foreachable($module_objects)) {
        $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'object');
        foreach ($module_objects as $module_object) {
          if (instance_of($module_object,'File')) {
            $revisions = $module_object->getRevisions();
            $output_builder->outputAttachments($revisions);
            
            $comments = $module_object->getComments($object_visibility);
            $output_builder->outputObjectsAttachments($comments);
            
          	$output_builder->smarty->assign('object',$module_object);
          	$output_builder->smarty->assign('comments',$comments);
          	$output_builder->outputToFile('file_'.$module_object->getId());
          } // if
        } // foreach
      } // if

      $this->serveData($output_builder->execution_log, 'execution_log', null, FORMAT_JSON);
    } // export
    
    /**
     * Show upload form
     *
     * @param void
     * @return null
     */
    function upload() {
      $this->wireframe->page_actions = array(); // clear page actions
      
      if(!File::canAdd($this->logged_user, $this->active_project)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $file_data = array(
        'milestone_id' => $this->request->get('milestone_id'),
        'visibility'   => $this->active_project->getDefaultVisibility()
      );
      
      if(instance_of($this->active_category, 'Category')) {
        $file_data['parent_id'] = $this->active_category->getId();
      } // if
      
      js_assign('files_section_url', files_module_url($this->active_project));
      
      require_once SMARTY_PATH . '/plugins/modifier.filesize.php';
      
      $this->smarty->assign(array(
        'file_data' => $file_data,
        'max_upload_size' => smarty_modifier_filesize(get_max_upload_size()),
        'upload_single_file_url' => assemble_url('project_files_upload_single', array('project_id' => $this->active_project->getId())),
      ));
    } // upload
    
    /**
     * Upload single file
     *
     * @param void
     * @return null
     */
    function upload_single() {
      if($this->request->isSubmitted()) {
        if(!File::canAdd($this->logged_user, $this->active_project)) {
          if($this->request->isApiCall()) {
            $this->httpError(HTTP_ERR_FORBIDDEN, null, true, true);
          } else {
            die('error - upload not permitted');
          } // if
        } // if
              
        $file_data = $this->request->post('file');
        if(!is_array($file_data)) {
          $file_data = array(
            'milestone_id' => $this->request->get('milestone_id'),
            'visibility' => $this->active_project->getDefaultVisibility()
          );
          if(instance_of($this->active_category, 'Category')) {
            $file_data['parent_id'] = $this->active_category->getId();
          } // if
        } // if
        $this->smarty->assign('file_data', $file_data);
  
        if($this->request->isSubmitted()) {
          db_begin_work();
          
          $this->active_file = new File();
          $attached = attach_from_files($this->active_file, $this->logged_user);
          
          // Do we have an upload error?
          if(is_error($attached) || $attached != 1) {
            if($this->request->isApiCall()) {
              $this->serveData(is_error($attached) ? $attached : new Error('0 files uploaded'));
            } else {
              die('error - nothing uploaded');
            } // if
          } // if
          
          $this->active_file->setAttributes($file_data);
          if($this->active_file->getName() == '') {
            $this->active_file->setName($this->active_file->pending_files[0]['name']);
          } // if
          
          $this->active_file->setRevision(1);
          $this->active_file->setProjectId($this->active_project->getId());
          $this->active_file->setCreatedBy($this->logged_user);
          $this->active_file->setState(STATE_VISIBLE);
          
          $save = $this->active_file->save();
          if($save && !is_error($save)) {
            if($this->active_file->countRevisions() > 0) {
              $subscribers = array($this->logged_user->getId());
              if(is_foreachable($this->request->post('notify_users'))) {
                $subscribers = array_merge($subscribers, $this->request->post('notify_users'));
              } else {
                $subscribers[] = $this->active_project->getLeaderId();
              } // if
              
              if(!in_array($this->active_project->getLeaderId(), $subscribers)) {
                $subscribers[] = $this->active_project->getLeaderId();
              } // if
              
              Subscriptions::subscribeUsers($subscribers, $this->active_file);
              
              db_commit();
              $this->active_file->ready();
              
              if($this->request->isApiCall()) {
                $this->serveData($this->active_file, 'file');
              } else {
                die('success'); // async
              } // if
            } else {
              if($this->request->isApiCall()) {
                $this->httpError(HTTP_ERR_OPERATION_FAILED, null, true, true);
              } else {
                die('error - unable to attach file');
              } // if
            } // if
          } else {
            if($this->request->isApiCall()) {
              $this->serveData($save);
            } else {
              die('error - could not save file object'); // async
            } // if
          } // if
        } // if
      } else {
        if($this->request->isApiCall()) {
          $this->httpError(HTTP_ERR_BAD_REQUEST, null, true, true);
        } else {
          die('error - request is not POST request'); // async
        } // if
      } // if
    } // upload_single
    
    /**
     * Update multiple tickets
     *
     * @param void
     * @return null
     */
    function mass_edit() {
      if (!$this->request->isSubmitted()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if

      $action = $this->request->post('with_selected');
      if(trim($action) == '') {
        flash_error('Please select what you want to do with selected tickets');
        $this->redirectToReferer($this->smarty->get_template_vars('files_url'));
      } // if

      $files_ids = $this->request->post('files');
      $object_types = $this->request->post('object_types');
      
      if ($object_types == 'files') {
        $files = Files::findByIds($files_ids, STATE_VISIBLE, $this->logged_user->getVisibility());
        $redirect_url = $this->smarty->get_template_vars('files_url');
      } else if ($object_types == 'attachments') {
        $files = Attachments::findbyids($files_ids, STATE_VISIBLE, $this->logged_user->getVisibility());
        $redirect_url = $this->smarty->get_template_vars('attachments_url');
      } else {
        $files = array();
        $redirect_url = $this->smarty->get_template_vars('files_url');
      } // if
      
      if (!is_foreachable($files)) {
        flash_error('Please select files that you would like to update');
        $this->redirectToReferer($this->smarty->get_template_vars('files_url'));
      } // if

      $updated = 0;
      
      if ($action == 'delete') {
        // delete attachments
          $message = lang(':count attachments deleted');
      	  foreach ($files as $file) {
      	    if ($file->canDelete($this->logged_user)) {
        	    $delete = $file->delete();
        	    if ($delete && !is_error($delete)) {
        	      $updated++;
        	    } // if
      	    } // if
      	  } // foreach
        
      } else if ($action == 'move_to_trash') {
        // move files to trash
      	  $message = lang(':count files moved to trash');
      	  foreach ($files as $file) {
      	    if ($file->canDelete($this->logged_user)) {
        	    $delete = $file->moveToTrash();
        	    if ($delete && !is_error($delete)) {
        	      $updated++;
        	    } // if
      	    } // if
      	  } // foreach

      } else if (str_starts_with($action, 'move_to_category')) {
        // chage files category
        $message = lang(':count files updated');
        if ($action == 'move_to_category') {
          $category_id = 0;
        } else {
          $category_id = (integer) substr($action, 17);
        } // if
        $category = $category_id ? Categories::findById($category_id) : null;
        
        foreach ($files as $file) {
          if ($file->canEdit($this->logged_user)) {
            $file->setParent($category, false);
            $save = $file->save();
            if ($save && !is_error($save)) {
              $updated++;
            } // if
          }
        } // foreach
      } else {
        // invalid action
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      }
      
      flash_success($message, array('count' => $updated));
      $this->redirectToReferer($redirect_url);
    } // mass_update
  
  } // FilesController

?>