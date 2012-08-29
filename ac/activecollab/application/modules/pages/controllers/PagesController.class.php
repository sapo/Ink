<?php

  // Extend ProjectsController
  use_controller('project', SYSTEM_MODULE);

  /**
   * Pages controller
   *
   * Main content controller for pages plugin. It implements content managament 
   * and browsing routings done on project level.
   * 
   * @package activeCollab.modules.pages
   * @subpackage controllers
   */
  class PagesController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    var $active_module = PAGES_MODULE;
    
    /**
     * Page instance
     *
     * @var Page
     */
    var $active_page;
    
    /**
     * Enable categories support for this controller
     *
     * @var boolean
     */
    var $enable_categories = true;
    
    /**
     * Actions that are available through API
     *
     * @var array
     */
    var $api_actions = array('index', 'view', 'add', 'edit', 'archive', 'unarchive');
  
    /**
     * Constructor
     *
     * @param Request $request
     * @return PagesController
     */
    function __construct($request) {
      parent::__construct($request);
      
      if($this->logged_user->getProjectPermission('page', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $section_url  = pages_module_url($this->active_project);
      
      $this->wireframe->addBreadCrumb(lang('Pages'), $section_url);
      if($this->active_category->isLoaded()) {
        $this->wireframe->addBreadCrumb($this->active_category->getName(), assemble_url('project_pages', array('project_id' => $this->active_project->getId(), 'category_id' => $this->active_category->getId())));
      } // if
      
      $page_id = $this->request->get('page_id');
      if($page_id) {
        $this->active_page = Pages::findById($page_id);
      } // if
      
      if(instance_of($this->active_page, 'Page')) {
        $parents = array();

        $parent = $this->active_page->getParent();
        while(instance_of($parent, 'ProjectObject')) {
          if(instance_of($parent, 'Page')) {
            if(array_key_exists($parent->getId(), $parents)) {
              break; // avoid dead loops
            } // if
  
            $parents[$parent->getId()] = $parent;
            $parent = $parent->getParent();
          } elseif(instance_of($parent, 'Category')) {
            $parents[$parent->getId()] = $parent;
            break;
          } else {
            break;
          } // if
        } // while

        $parents = array_reverse($parents);
        foreach($parents as $parent) {
          if(instance_of($parent, 'Page')) {
            $this->wireframe->addBreadCrumb($parent->getName(), $parent->getViewUrl());
          } elseif(instance_of($parent, 'Category')) {
            $this->wireframe->addBreadCrumb($parent->getName(), assemble_url('project_pages', array('project_id' => $this->active_project->getId(), 'category_id' => $parent->getId())));
          } // if
        } // foreach
        
        $this->wireframe->addBreadCrumb($this->active_page->getName(), $this->active_page->getViewUrl());
      } else {
        $this->active_page = new Page();
      } // if
      
      if(Page::canAdd($this->logged_user, $this->active_project)) {
        if($this->active_page->isLoaded()) {
          $add_page_url = pages_module_add_page_url($this->active_project, array('parent' => $this->active_page));
        } elseif($this->active_category->isLoaded()) {
          $add_page_url = pages_module_add_page_url($this->active_project, array('parent' => $this->active_category));
        } else {
          $add_page_url = pages_module_add_page_url($this->active_project);
        } // if
        
        $this->wireframe->addPageAction(lang('New Page'), $add_page_url);
      } // if
      
      $this->smarty->assign(array(
        'active_page'  => $this->active_page,
        'pages_url'    => $section_url,
        'add_page_url' => $add_page_url,
        'page_tab'     => 'pages',
      ));
    } // __construct
    
    /**
     * Render pages associtate with a specific project, pages homepage
     *
     * @param void
     * @return null
     */
    function index() {
      if($this->request->isApiCall()) {
        if($this->active_category->isLoaded()) {
          $this->serveData(Pages::findByCategory($this->active_category, STATE_VISIBLE, $this->logged_user->getVisibility()), 'pages');
        } else {
          $this->serveData(Categories::findByModuleSection($this->active_project, PAGES_MODULE, 'pages'), 'categories');
        } // if
      } else {
        $per_page = 30;
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        if($this->active_category->isLoaded()) {
          $this->setTemplate(array(
            'module' => PAGES_MODULE,
            'controller' => 'pages',
            'template' => 'category'
          ));
          
          $this->wireframe->addPageAction(lang('Reorder Pages'), assemble_url('project_pages_reorder', array('project_id' => $this->active_project->getId(), 'category_id' => $this->active_category->getId())), null, array('id' => 'reorder_pages_button'));
          
          $this->smarty->assign('pages', Pages::findByCategory($this->active_category, STATE_VISIBLE, $this->logged_user->getVisibility()));
        } else {
          list($pages, $pagination) = Pages::paginateByProject($this->active_project, $page, $per_page, STATE_VISIBLE, $this->logged_user->getVisibility());
          
          $grouped_pages = group_by_date($pages, $this->logged_user, 'getUpdatedOn', true);
          $this->smarty->assign(array(
            'grouped_pages' => $grouped_pages,
            'pagination' => $pagination,
          ));
        } // if
        
        $this->smarty->assign(array(
        	'categories' => Categories::findByModuleSection($this->active_project, PAGES_MODULE, 'pages'),
        	'can_manage_categories' => $this->logged_user->isProjectLeader($this->active_project) || $this->logged_user->isProjectManager(), 
        ));
      } // if
    } // index
    
    /**
     * View category
     * 
     * This action overrides default category listing page and redirects user to 
     * the Pages module page where content of the category is listed
     *
     * @param void
     * @return null
     */
    function view_category() {
      if($this->active_category->isLoaded()) {
        $this->redirectTo('project_pages', array('project_id' => $this->active_project->getId(), 'category_id' => $this->active_category->getId()));
      } else {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
    } // view_category
    
    /**
     * View specific page
     *
     * @param void
     * @return null
     */
    function view() {
      if($this->active_page->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND, null, true, $this->request->isApiCall());
      } // if
      
      if(!$this->active_page->canView($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      if($this->request->isApiCall()) {
        $this->serveData($this->active_page, 'page', array('describe_comments' => true, 'describe_tasks' => true, 'describe_attachments' => true, 'describe_subpages' => true, 'describe_revisions' => true));
      } else {
        ProjectObjectViews::log($this->active_page, $this->logged_user);
        
        $page = (integer) $this->request->get('page');
        if($page < 1) {
          $page = 1;
        } // if
        
        list($comments, $pagination) = $this->active_page->paginateComments($page, $this->active_page->comments_per_page, $this->logged_user->getVisibility());
        
        $this->smarty->assign(array(
          'parent' => $this->active_page->getParent(),
          'subpages' => $this->active_page->getSubpages($this->logged_user->getVisibility()),
          'versions' => $this->active_page->getVersions(),
          'comments' => $comments,
          'pagination' => $pagination,
        ));
      } // if
    } // view
    
    /**
     * Revert to a specific version
     *
     * @param void
     * @return null
     */
    function revert() {
      if($this->active_page->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if($this->request->isSubmitted()) {
        $to_version = null;
        
        $to_version_num = (integer) $this->request->get('to');
        if($to_version_num) {
          $to_version = PageVersions::findById(array(
            'page_id' => $this->active_page->getId(),
            'version' => $to_version_num,
          ));
        } // if
        
        if(!instance_of($to_version, 'PageVersion')) {
          $this->httpError(HTTP_ERR_NOT_FOUND);
        } // if
        
        $old_name = $this->active_page->getName();
        
        $revert = $this->active_page->revertToVersion($to_version);
        if($revert && !is_error($revert)) {
          flash_success('Page ":name" has been reverted to version #:version', array('name' => $old_name, 'version' => $to_version->getVersion()));
        } else {
          flash_success('Failed to revert ":name" page to version #:version', array('name' => $old_name, 'version' => $to_version->getVersion()));
        } // if
        
        $this->redirectToUrl($this->active_page->getViewUrl());
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // revert
    
    /**
     * Compare page versions
     *
     * @param void
     * @return null
     */
    function compare_versions() {
      $this->skip_layout = $this->request->isAsyncCall();
      
      if($this->active_page->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      // Load new version
      $new_version_string = $this->request->get('new');
      if(empty($new_version_string) || $new_version_string == 'latest') {
        $new_version = $this->active_page;
        $new_version_string = 'latest';
      } else {
        $new_version = PageVersions::findById(array(
          'page_id' => $this->active_page->getId(),
          'version' => $new_version_string,
        ));
      } // if
      
      if(!instance_of($new_version, 'Page') && !instance_of($new_version, 'PageVersion')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      // Load old version
      $old_version_string = $this->request->get('old');
      if(empty($old_version_string)) {
        $old_version = PageVersions::findPrevious($this->active_page);
        if($old_version) {
          $old_version_string = $old_version->getVersion();
        } // if
      } elseif($old_version_string == 'latest') {
        $old_version = $this->active_page;
      } else {
        $old_version = PageVersions::findById(array(
          'page_id' => $this->active_page->getId(),
          'version' => $old_version_string,
        ));
      } // if
      
      if(!instance_of($old_version, 'Page') && !instance_of($old_version, 'PageVersion')) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      // Lets do the diff
      require_once ANGIE_PATH . '/classes/diff/init.php';
      
      $name_diff = render_diff($old_version->getName(), $new_version->getName());
      if(empty($name_diff)) {
        $name_diff = $old_version->getName();
      } // if
      
      $body_diff = render_diff(html_to_text($old_version->getFormattedBody()), html_to_text($new_version->getFormattedBody()));
      if(empty($body_diff)) {
        $body_diff = html_to_text($old_version->getFormattedBody());
      } // if
      
      // Display
      $this->smarty->assign(array(
        'old_version_string' => $old_version_string,
        'new_version_string' => $new_version_string,
        'new_version' => $new_version,
        'old_version' => $old_version,
        'name_diff' => $name_diff,
        'body_diff' => $body_diff,
      ));
      
      js_assign('compare_pages_url', $this->active_page->getCompareVersionsUrl());
    } // compare_versions
    
    /**
     * Show and process add page form
     *
     * @param void
     * @return null
     */
    function add() {
      $this->wireframe->print_button = false;
      
      if(!Page::canAdd($this->logged_user, $this->active_project)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
      } // if
      
      $parent_id = (integer) $this->request->get('parent_id');
      
      $page_data = $this->request->post('page');
      if(!is_array($page_data)) {
        $page_data = array(
          'parent_id' => $this->request->get('parent_id'),
          'milestone_id' => $this->request->get('milestone_id'),
          'visibility' => $this->active_project->getDefaultVisibility()
        );
      } // if
      
      $this->smarty->assign('page_data', $page_data);
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_page = new Page();
        
        attach_from_files($this->active_page, $this->logged_user);
        
        $this->active_page->setAttributes($page_data);
        $this->active_page->setProjectId($this->active_project->getId());
        $this->active_page->setCreatedBy($this->logged_user);
        $this->active_page->setUpdatedOn(DateTimeValue::now());
        $this->active_page->setUpdatedBy($this->logged_user);
        $this->active_page->setState(STATE_VISIBLE);
        
        $save = $this->active_page->save();
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
          
          Subscriptions::subscribeUsers($subscribers, $this->active_page);
          
          db_commit();
          $this->active_page->ready();
          
          if($this->request->isApiCall()) {
            $this->serveData($this->active_page, 'page');
          } else {
            flash_success('Page ":name" has been created', array('name' => $this->active_page->getName()));
            $this->redirectToUrl($this->active_page->getViewUrl());
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
    } // add
    
    /**
     * Quick add checklist
     *
     * @param void
     * @return null
     */
    function quick_add() {
      if(!Page::canAdd($this->logged_user, $this->active_project)) {
      	$this->httpError(HTTP_ERR_FORBIDDEN, lang("You don't have permission for this action"), true, true);
      } // if
      
      $this->skip_layout = true;
      
      $page_data = $this->request->post('page');
      if (!is_array($page_data)) {
        $page_data = array(
          'visibility' => $this->active_project->getDefaultVisibility()
        );
      } // if
      $this->smarty->assign(array(
        'page_data' => $page_data,
        'quick_add_url' => assemble_url('project_pages_quick_add', array('project_id' => $this->active_project->getId())),
      ));
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        
        $this->active_page = new Page();
        
        if (count($_FILES > 0)) {
          attach_from_files($this->active_page, $this->logged_user);  
        } // if
        
        $this->active_page->setAttributes($page_data);
        $this->active_page->setBody(clean(array_var($page_data, 'body', null)));
        $this->active_page->setProjectId($this->active_project->getId());
        $this->active_page->setCreatedBy($this->logged_user);
        $this->active_page->setState(STATE_VISIBLE);
        
        $save = $this->active_page->save();
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
          Subscriptions::subscribeUsers($subscribers, $this->active_page);
            
          db_commit();
          $this->active_page->ready();
          
          $this->smarty->assign(array(
            'active_page' => $this->active_page,
            'page_data' => array('visibility' => $this->active_project->getDefaultVisibility()),
            'project_id' => $this->active_project->getId()
          ));
        } else {
          db_rollback();
          $this->httpError(HTTP_ERR_OPERATION_FAILED, $save->getErrorsAsString(), true, true);
        } // if
      } // if
    } // quick_add
    
    /**
     * Show and process edit page form. Also, handle all other page update 
     * requests
     *
     * @param void
     * @return null
     */
    function edit() {
      $this->wireframe->print_button = false;
      
      if($this->active_page->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_page->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      $page_data = $this->request->post('page');
      if(!is_array($page_data)) {
        $page_data = array(
          'name' => $this->active_page->getName(),
          'body' => $this->active_page->getBody(),
          'visibility' => $this->active_page->getVisibility(),
          'parent_id' => $this->active_page->getParentId(),
          'milestone_id' => $this->active_page->getMilestoneId(),
          'tags' => $this->active_page->getTags(),
        );
      } // if
      
      $this->smarty->assign(array(
        'page_data' => $page_data,
      ));
      
      if($this->request->isSubmitted()) {
        db_begin_work();
        $old_page_name = $this->active_page->getName();
        $old_page_body = $this->active_page->getBody();
        
        $this->active_page->setAttributes($page_data);
        
        $new_version = null;
        $error = null;
        
        // Create a new version
        if((!array_var($page_data, 'is_minor_revision', false) && ($this->active_page->getName() != $old_page_name || $this->active_page->getBody() != $old_page_body))) {
          $new_version = $this->active_page->createVersion($this->logged_user);
          if(is_error($new_version)) {
            $error = $new_version;
          } // if
        } // if
        
        // Update page properties if we don't have an error already
        if(!is_error($error)) {
          $save = $this->active_page->save();
          if(is_error($save)) {
            $error = $save;
          } // if
        } // if
        
        if(!is_error($error)) {
          if($new_version) {
            event_trigger('on_new_revision', array(&$this->active_page, &$new_version, &$this->logged_user));
            
            $activity_log = new NewPageVersionActivityLog();
            $activity_log->log($this->active_page, $this->logged_user);
          } // if
          
          Subscriptions::subscribe($this->logged_user, $this->active_page);
          
          db_commit();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            flash_success('Page ":name" has been updated', array('name' => $old_page_name));
            $this->redirectToUrl($this->active_page->getViewUrl());
          } else {
            $this->serveData($this->active_page, 'page');
          } // if
        } else {
          db_rollback();
          
          if($this->request->getFormat() == FORMAT_HTML) {
            $this->smarty->assign('errors', $error);
          } else {
            $this->serveData($error);
          } // if
        } // if
      } // if
    } // edit
    
    /**
     * Mark this page as archived
     *
     * @param void
     * @return null
     */
    function archive() {
      if($this->active_page->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_page->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        $this->active_page->setIsArchived(true);
        $save = $this->active_page->save();
        
        if($save && !is_error($save)) {
          if($this->request->isApiCall()) {
            $this->serveData($this->active_page, 'page');
          } else {
            flash_success('Page ":name" has been archived', array('name' => $this->active_page->getName()));
          } // if
        } else {
          if($this->request->isApiCall()) {
            $this->serveData($save);
          } else {
            flash_error('Failed to archive ":name" page', array('name' => $this->active_page->getName()));
          } // if
        } // if
        
        $this->redirectToUrl($this->active_page->getViewUrl());
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // archive
    
    /**
     * Remove this page from archive
     *
     * @param void
     * @return null
     */
    function unarchive() {
      if($this->active_page->isNew()) {
        $this->httpError(HTTP_ERR_NOT_FOUND);
      } // if
      
      if(!$this->active_page->canEdit($this->logged_user)) {
        $this->httpError(HTTP_ERR_FORBIDDEN);
      } // if
      
      if($this->request->isSubmitted()) {
        $this->active_page->setIsArchived(false);
        $save = $this->active_page->save();
        
        if($save && !is_error($save)) {
          if($this->request->isApiCall()) {
            $this->serveData($this->active_page, 'page');
          } else {
            flash_success('Page ":name" has been archived', array('name' => $this->active_page->getName()));
          } // if
        } else {
          if($this->request->isApiCall()) {
            $this->serveData($save);
          } else {
            flash_error('Failed to archive ":name" page', array('name' => $this->active_page->getName()));
          } // if
        } // if
        
        $this->redirectToUrl($this->active_page->getViewUrl());
      } else {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
    } // unarchive
    
    /**
     * Export project pages
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
      
      $module_categories = Categories::findByModuleSection($this->active_project, PAGES_MODULE, 'pages');
      
      $output_builder->smarty->assign(array(
        'categories' => $module_categories,
        'visibility' => $object_visibility,
      ));
      $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'index');
      $output_builder->outputToFile('index');
      
      if (is_foreachable($module_categories)) {
        foreach ($module_categories as $module_category) {
        	$output_builder->smarty->assign('current_category', $module_category);
        	$output_builder->smarty->assign('objects', Pages::findByCategory($module_category, STATE_VISIBLE, $object_visibility));
          $output_builder->outputToFile('category_'.$module_category->getId());
        } // foreach
      } // if
      
      $pages = ProjectObjects::find(array(
        "conditions" => array("project_id = ? AND module = ? AND type = ?", $this->active_project->getId(), 'pages', 'Page')
      ));
     
      $page_ids = array();
      if (is_foreachable($pages)) {
        $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'object');
        foreach ($pages as $page) {
          if (instance_of($page, 'Page')) {
            $page_ids[] = $page->getId();
            $parent = $page->getParent();
            $comments = $page->getComments($object_visibility);
            $output_builder->smarty->assign(array(
              'page' => $page,
              'subpages' => $page->getSubpages($object_visibility),
              'parent' => $parent,
              'comments' => $comments,
            ));
            
            if (instance_of($parent,'Page')) {
              $output_builder->smarty->assign('parent_url', './page_'.$parent->getId().'.html');
            } else if (instance_of($parent, 'Category')) {
              $output_builder->smarty->assign('parent_url', './category_'.$parent->getId().'.html');
            } // if
            
            $output_builder->outputToFile('page_'.$page->getId());
            $output_builder->outputObjectsAttachments($comments);
            $output_builder->outputAttachments($page->getAttachments());
          } // if
        } // foreach
        
        $revisions = PageVersions::findByPageIds($page_ids);
        if (is_foreachable($revisions)) {
          $output_builder->setFileTemplate($this->active_module, $this->controller_name, 'revision');
          foreach ($revisions as $revision) {
            $output_builder->smarty->assign('revision',$revision);
            $output_builder->outputToFile('revision_'.$revision->getPageId().'_'.$revision->getVersion());
          } // foreach
        } // if
      } // if
      
      $this->serveData($output_builder->execution_log, 'execution_log', null, FORMAT_JSON);
    } // export
    
    /**
     * Reorder pages
     * 
     * @param void
     * @return null
     */
    function reorder() {
      $this->skip_layout = $this->request->isAsyncCall();
      
      if (!$this->active_category->isLoaded()) {
        $this->httpError(HTTP_ERR_BAD_REQUEST);
      } // if
            
      $ordered_pages = $this->request->post('ordered_pages');
      
      $layout_pages = Pages::findByCategory($this->active_category, STATE_VISIBLE, $this->logged_user->getVisibility());
      
      js_assign('is_async_call', $this->request->isAsyncCall());
      $this->smarty->assign(array(
        'pages'             =>  $layout_pages,
        'opened'            =>  implode(',',$opened),
        'reorder_pages_url' =>  assemble_url('project_pages_reorder', array('project_id' => $this->active_project->getId(), 'category_id' => $this->active_category->getId()))
      ));
      
      if ($this->request->isSubmitted()) {
        if (is_foreachable($ordered_pages)) {    
          $sorted_pages = array(); $positions = array();
          foreach ($ordered_pages as $ordered_page_id => $ordered_page_parent_id) {
            $ordered_page_parent_id = $ordered_page_parent_id ? $ordered_page_parent_id : $this->active_category->getId();
            $position = array_var($positions, $ordered_page_parent_id, 1);
            $sorted_pages[$ordered_page_id] = array(
              'position' => $position,
              'parent_id' => $ordered_page_parent_id
            );
            $position++;
            $positions[$ordered_page_parent_id] = $position;
          } // if
          
          $pages = Pages::findByIds(array_keys($ordered_pages), STATE_VISIBLE, $this->logged_user->getVisibility());
          if (is_foreachable($pages)) {
            foreach ($pages as $page) {
              if (isset($sorted_pages[$page->getId()])) {
                $page->setPosition(array_var($sorted_pages[$page->getId()],'position'));
                $parent_id = array_var($sorted_pages[$page->getId()],'parent_id');
                if ($parent_id) {
                  $page->setParentId($parent_id);
                } // if
                $page->save();
              } // if
            } // foreach
          } // if
        } // if
        
        if ($this->request->isAsyncCall()) {
          $per_page = 30;
          $page = (integer) $this->request->get('page');
          if($page < 1) {
            $page = 1;
          } // if
          
          if($this->active_category->isLoaded()) {
            $this->setTemplate(array(
              'module' => PAGES_MODULE,
              'controller' => 'pages',
              'template' => 'category'
            ));
            $this->smarty->assign(array(
              'pages' => Pages::findByCategory($this->active_category, STATE_VISIBLE, $this->logged_user->getVisibility()),
            	'categories' => Categories::findByModuleSection($this->active_project, PAGES_MODULE, 'pages'),
	            'can_manage_categories' => $this->logged_user->isProjectLeader($this->active_project) || $this->logged_user->isProjectManager(),
            ));
          } // if
        } // if
      } // if submitted      
    } // reorder
  
  } // PagesController

?>