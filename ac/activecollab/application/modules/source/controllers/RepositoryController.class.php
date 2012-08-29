<?php

use_controller('project', SYSTEM_MODULE);

/**
 * Repository controller
 * 
 * @package activeCollab.modules.source
 * @subpackage controllers
 */
class RepositoryController extends ProjectController {

  /**
   * Controller name
   *
   * @var string
   */
  var $controller_name = 'repository';

  /**
   * Active module
   *
   * @var constant
   */
  var $active_module = SOURCE_MODULE;

  /**
   * Active repository
   *
   * @var Repository
   */
  var $active_repository = null;
   
  /**
   * Active file
   *
   * @var string
   */
  var $active_file = null;
  
  /**
   * Active file basename
   *
   * @var string
   */
  var $active_file_basename = null;
  
  /**
   * Active revision
   *
   * @var integer
   */
  var $active_revision = null;
  
  /**
   * Active commit
   * 
   * @var Commit
   */
  var $active_commit = null;

  /**
   * Enable categories support for this controller
   *
   * @var boolean
   */
  var $enable_categories = true;


  /**
   * Repository engine
   *
   * @var Repository engine
   */
  var $repository_engine = null;

  /**
   * Class constructor
   *
   * @param unknown_type $request
   */
  function __construct($request) {
    parent::__construct($request);
    
    if($this->logged_user->getProjectPermission('repository', $this->active_project) < PROJECT_PERMISSION_ACCESS) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    $source_module_url = source_module_url($this->active_project);
    $add_repository_url = source_module_add_repository_url($this->active_project);
    
    // wireframe
    $this->wireframe->addBreadCrumb(lang('Source'), $source_module_url);
    $this->wireframe->print_button = false;
    
    $repository_id = $this->request->get('repository_id');
    $this->active_repository = Repositories::findById($repository_id);
    
    if (instance_of($this->active_repository, 'Repository')) {
      // load repository engine
      if (!$this->active_repository->loadEngine()) {
        flash_error('Failed to load repository engine class');
        $this->redirectToUrl($source_module_url);
      } // if
      $this->repository_engine = new RepositoryEngine($this->active_repository);
      
      $this->active_repository->mapped_users = SourceUsers::findByRepository($this->active_repository);
      
      if (!$this->repository_engine->executableExists()) {
        $this->wireframe->addPageMessage(lang("Source executable not found. You won't be able to use this module"), 'error');
      } // if
      
      // active commit
      $this->active_revision = intval($this->request->get('r'));
      $this->active_commit = Commits::findByRevision($this->active_revision, $this->active_repository);
      
      js_assign('update_url', $this->active_repository->getupdateurl());
      js_assign('active_revision', intval($this->request->get('r')));
      
      if (!$this->active_repository->isNew()) {
        $this->wireframe->addBreadCrumb(clean($this->active_repository->getName()), $this->active_repository->getHistoryUrl());
      } // if
    } else {
      $this->active_repository = new Repository();
      $this->active_repository->setRepositoryType(1);
      $this->active_repository->loadEngine();
      $this->repository_engine = new RepositoryEngine($this->active_repository);
    } // if
        
    if (!instance_of($this->active_commit, 'Commit')) {
      $this->active_commit = new Commit();
    } // if
    
    // active file
    $this->active_file = urldecode($this->request->get('path'));
    $path_info = pathinfo($this->active_file);
    $this->active_file_basename = array_var($path_info, 'basename', null);

    // smarty stuff
    $this->smarty->assign(array(
      'project_tab' => SOURCE_MODULE,
      'active_repository' => $this->active_repository,
      'active_revision' => $this->active_revision,
      'active_commit' => $this->active_commit,
      'active_file' => $this->active_file,
      'active_file_basename' => $this->active_file_basename,
      'page_tab' => 'source',
      'add_repository_url' => $add_repository_url
    ));
  } // __construct
  
  /**
   * List repositories
   *
   * @param null
   * @return void
   */
  function index() {
    if(Repository::canAdd($this->logged_user, $this->active_project)) {
      $this->wireframe->addPageAction(lang('Add Repository'), source_module_add_repository_url($this->active_project));
    } // if

    $repositories = Repositories::findByProjectId($this->active_project->getId());

    $this->smarty->assign(array(
      'repositories' => Repositories::findByProjectId($this->active_project->getId()),
    ));
  } // index
  
  /**
   * Get item info
   *
   * @param null
   * @return void
   */
  function info() {
    if(!$this->active_repository->canView($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    if ($this->active_commit->isNew()) {
      $this->active_commit = $this->active_repository->getLastCommit();
      if (instance_of($this->active_commit, 'Commit')) {
        $this->active_revision = $this->active_commit->getRevision();
      } else {
        $this->active_revision = null;
      } // if
    } // if
    
    $this->skip_layout = true;
    $this->repository_engine->triggerred_by_handler = true;
    $info = $this->repository_engine->getInfo($this->active_file, $this->active_revision, true, $this->request->get('peg'));
    $properties = $this->repository_engine->getProperties($this->active_file, $this->active_revision, $this->request->get('peg'));
    $this->smarty->assign(array(
      'error' => !is_null($this->repository_engine->error),
      'error_message' => $this->repository_engine->error,
      'info'  => $info . "\n\n" . $properties,
      'item_path' => $this->active_file,
      'item_name'  => $this->active_file_basename,
      'item_revision' => $this->active_revision
    ));
    
  } // info
  
  /**
   * View commit history of selected repository
   *
   * @param null
   * @return void
   */
  function history() {
    if(!$this->active_repository->canView($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    $this->wireframe->addPageAction(lang('Browse repository'), $this->active_repository->getBrowseUrl(), null);
    
    if ($this->active_repository->canEdit($this->logged_user) || ($this->active_repository->getCreatedById() == $this->logged_user->getId())) {
      $this->wireframe->addPageAction(lang('Update'), $this->active_repository->getUpdateUrl(), null, array('id' => 'repository_ajax_update'));
    }
    
    $per_page = 20;
    $page = intval(array_var($_GET, 'page')) > 0 ? array_var($_GET, 'page') : 1;
    
    $commits_count_params = array("parent_id = ?", $this->active_repository->getId());
    
    $filter_by_author = $this->request->get('filter_by_author');
    if (!is_null($filter_by_author)) {
      $commits_count_params['created_by_name'] = $filter_by_author;
    } // if

    list($commits, $pagination) = Commits::paginateByRepository($this->active_repository, $page, $per_page, $filter_by_author);

    if (is_foreachable($commits)) {
      foreach ($commits as $commit) {
        $commit->total_paths = count($commit->getPaths());
        $commit->setPaths($this->repository_engine->groupPaths($commit->getPaths()));
      } // foreach
      
      if (!is_null($filter_by_author)) {
        $filter_by_author = array();
        $filter_by_author['user_object'] = $commits['0']->getAuthor();
        $filter_by_author['user_name'] = $commits['0']->getCreatedbyName();
      } // if
      
    } // if
    
    $commits = group_by_date($commits);
    
    $this->smarty->assign(array(
      'filter_by_author'  => $filter_by_author,
      'commits'           => $commits,
      'pagination'        => $pagination,
      'project'           => $this->active_project,
      'total_commits'     => Commits::count($commits_count_params),
    ));
  } // history
  
  /**
   * Commit info
   *
   * @param null
   * @return void
   */
  function commit() {
    if(!$this->active_repository->canView($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    if (!instance_of($this->active_commit, 'Commit')) {
      $this->httpError(HTTP_ERR_NOT_FOUND);
    } // if

    $this->wireframe->addPageAction(lang('Revision history'), $this->active_repository->getHistoryUrl());
    $this->wireframe->addPageAction(lang('Browse repository'), $this->active_repository->getBrowseUrl());
    
    $grouped_paths = RepositoryEngine::groupPaths($this->active_commit->getPaths());
    ksort($grouped_paths);

    $diff = $this->active_commit->getDiff();
    if (!is_array($diff)) {
      $diff = $this->repository_engine->getCommitDiff($this->active_revision, $this->active_file);
      if (is_array($diff)) {
        $this->active_commit->setDiff($diff);
        $this->active_commit->setCreatedBy(new AnonymousUser($this->active_commit->getCreatedByName(),''));
        $save = $this->active_commit->save();
      } else {
        flash_error("Unable to retrieve diff information for selected commit");
        $this->redirectToReferer(source_module_url($this->active_project));
      } // if
    } // if
  
    $parsed = $this->repository_engine->parseDiff($diff);
    if (is_foreachable($parsed)) {
      for ($x=0; $x<count($parsed); $x++) {
        $filename = substr($parsed[$x]['file'],0,1) == '/' ? substr($parsed[$x]['file'],1) : '/'.$parsed[$x]['file'];
        
        if (!in_array($filename, $grouped_paths[SOURCE_MODULE_STATE_MODIFIED])) {
          unset($parsed[$x]);
        } // if
      } // for
    } // if
    $parsed = array_values($parsed);
    
    ProjectObjectViews::log($this->active_commit, $this->logged_user);
    
    $this->smarty->assign(array(
      'grouped_paths' => $grouped_paths,
      'diff'          => $parsed,
    ));
  } // commit info
  
  
  /**
   * Get project objects affected by a commit
   * 
   * @param null
   * @return void
   */
  function project_object_commits() {
    $project_object_id = array_var($_GET, 'object_id');
    $project_object = ProjectObjects::findById($project_object_id);
    
    if (!instance_of($project_object, 'ProjectObject')) {
      flash_error('Requested object does not exist');
      $this->redirectToReferer(source_module_url($this->active_project));
    }
    
    $this->wireframe->addBreadCrumb($project_object->getType(). ' ' . $project_object->getName(), $project_object->getViewUrl());
    
    $this->smarty->assign(array(
      'commits' => CommitProjectObjects::findCommitsByObject($project_object, $this->active_project),
      'active_object'  => $project_object
    ));
  } // commit_project_objects
  
  /**
   * Browse repository
   *
   * @param null
   * @return void
   */
  function browse() {
    if ($this->active_repository->isNew()) {
      $this->httpError(HTTP_ERR_NOT_FOUND);
    } // if
    
    if(!$this->active_repository->canView($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    if ($this->active_commit->isNew()) {
      $this->active_commit = $this->active_repository->getLastCommit();
      if (instance_of($this->active_commit, 'Commit')) {
        $this->active_revision = $this->active_commit->getRevision();
      } else {
        $this->active_revision = is_null(array_var($_GET, 'r')) ? $this->active_commit->getRevision() : array_var($_GET, 'r');
      } // if
    } // if
    
    $peg_revision = $this->request->get('peg');
    
    if (!$this->active_commit || $this->active_commit->isNew()) {
      flash_error('This repository is not updated yet. You need to update it first before you can browse it.');
      $this->redirectToReferer($this->active_repository->getViewUrl());
    } // if
    
    // wireframe stuff
    $this->wireframe->addPageAction(lang('Change Revision'), '#', null, array('id' => 'change_revision'));
    $this->wireframe->addPageAction(lang('Commit History'), $this->active_repository->getHistoryUrl());
    
    // path info
    $path_info = $this->repository_engine->getInfo($this->active_file, $this->active_revision, false, $peg_revision);
    // file source
    $file_source = file_source_can_be_displayed($path_info['path']) ? $this->repository_engine->cat($this->active_revision, $this->active_file, $peg_revision) : false;
    $file_path_info = pathinfo($this->active_file);
    $parent_directory = array_var($file_path_info, 'dirname', '');
    
    $latest_revision = $path_info['revision'];
    $file_latest_revision = Commits::findByRevision($latest_revision, $this->active_repository);   
    if (!instance_of($file_latest_revision, 'Commit')) {
      $file_latest_revision = $this->active_commit;
    } // if
    
    // check if path is directory or file
    if ($path_info['type'] == 'directory') {
      /**** DIRECTORY ****/      
      $this->smarty->assign(array(
        'list'              => $this->repository_engine->browse($this->active_revision, $this->active_file, $peg_revision),
        'parent_directory'  => $parent_directory == "/" ? "" : $parent_directory,
        'can_go_up'         => array_var($file_path_info, 'basename') !== ''
      ));
      
      // custom template
      $this->setTemplate(array(
        'module' => SOURCE_MODULE,
        'controller' => $this->controller_name,
        'template' => 'browse_directory',
      ));
    } else {
      /**** FILE ****/

      $this->smarty->assign(array(
        'lines'   => implode("\n", range(1, count($file_source))),
        'source'  => $file_source !== false ? implode("\n",$file_source) : $file_source,
      ));
      
      // custom template
      $this->setTemplate(array(
        'module' => SOURCE_MODULE,
        'controller' => $this->controller_name,
        'template' => 'browse_file',
      ));
      
      js_assign('compare_url', $this->active_repository->getFileCompareUrl($this->active_commit, $this->active_file, $this->request->get('peg')));
    } // if
    
    js_assign('browse_url', $this->active_repository->getBrowseUrl(null, $this->active_file, $this->request->get('peg')));

    // general template vars for both directory and file
    $this->smarty->assign(array(
      'navigation'  => $this->repository_engine->linkify($this->active_revision, $this->active_file),
      'latest_revision' => $file_latest_revision,
      'active_commit' => $this->active_commit,
      'active_revision' => $this->active_revision,
    ));

  } // browse repository
  
  /**
   * View commit history for a file
   *
   * @param null
   * @return void
   */
  function file_history() {
    if ($this->active_repository->isNew()) {
      $this->httpError(HTTP_ERR_NOT_FOUND);
    } // if
    
    if (!$this->active_revision) {
      $this->httpError(HTTP_ERR_NOT_FOUND);
    } // if
    
    if(!$this->active_repository->canView($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    // wireframe
    $this->wireframe->addPageAction(lang('Commit history'), $this->active_repository->getHistoryUrl());
    
    $logs = $this->repository_engine->getFileHistory($this->active_revision, $this->active_file, $this->request->get('peg'));
    $file_latest_revision = null;
    if (is_foreachable($logs)) {
      $latest_revision = array_var(first($logs), 'revision', null);
      if ($latest_revision !== null) {
        $file_latest_revision = Commits::findByRevision($latest_revision, $this->active_repository);
      } // if
    } // if
    if (!instance_of($file_latest_revision, 'Commit')) {
      $file_latest_revision = $this->active_commit;
    } // if
    
    $commit_ids = array();
    if (is_foreachable($logs)) {
       foreach ($logs as $log) {
        $commit_ids[] = array_var($log, 'revision');
       } // if
    } // if
    $commits = Commits::findByRevisionIds($commit_ids, $this->active_repository);

    $this->smarty->assign(array(
      'commits'     => $commits,
      'file'        => $this->active_file,
      'revision'    => $this->active_revision,
      'navigation'  => $this->repository_engine->linkify($this->active_revision, $this->active_file),
      'current'     => 'history',
      'latest_revision' => $file_latest_revision,
    ));
    
    js_assign('compare_url', $this->active_repository->getFileCompareUrl($this->active_commit, $this->active_file, $this->request->get('peg')));
  } // file history
  
  /**
   * Download file
   * 
   * @param null
   * @return void
   */
  function file_download() {
    if (!$this->active_file) {
      $this->httpError(HTTP_BAD_REQUEST);
    } // if
    
    if(!$this->active_repository->canView($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    $file_source = implode("\n", $this->repository_engine->cat($this->active_revision, $this->active_file, $this->request->get('peg')));
    download_contents($file_source, 'application/octet-stream', $this->active_file_basename, true);
  } // download file
  
  /**
   * Compare two revisions of a file
   *
   * @param null
   * @return void
   */
  function compare() {
    if(!$this->active_repository->canView($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    // wireframe
    $this->wireframe->addPageAction(lang('Commit history'), $this->active_repository->getHistoryUrl());
    
    $compare_to = $this->request->get('compare_to');
    $compared = Commits::findByRevision($compare_to, $this->active_repository);
    
    if (!instance_of($compared, 'Commit')) {
      flash_error('Revision does not exist');
      $this->redirectToReferer($this->active_repository->getFileHistoryUrl($this->active_revision, $this->active_file, $this->request->get('peg')));
    } // if
    
    // path info
    $path_info = $this->repository_engine->getInfo($this->active_file, $this->active_revision, false, $this->request->get('peg'));
    $latest_revision = $path_info['revision'];
    $file_latest_revision = Commits::findByRevision($latest_revision, $this->active_repository);   
    if (!instance_of($file_latest_revision, 'Commit')) {
      $file_latest_revision = $this->active_commit;
    } // if
    
    $diff_data = $this->repository_engine->compareToRevision($this->active_file, $compared->getRevision(), $this->active_revision, $this->request->get('peg'));
    $diff_changes = $this->repository_engine->parseDiff($diff_data);

    $this->smarty->assign(array(
      'compared'            => $compared,
      'diff'                => $diff_changes,
      'navigation'          => $this->repository_engine->linkify($this->active_revision, $this->active_file),
      'latest_revision'     => $file_latest_revision,
    ));
    
    js_assign('compare_url', $this->active_repository->getFileCompareUrl($this->active_commit, $this->active_file, $this->request->get('peg')));

  } // compare

  /**
   * Add a repository
   *
   * @param null
   * @return void
   */
  function add() {
    if(!Repository::canAdd($this->logged_user, $this->active_project)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    if (!$this->repository_engine->executableExists()) {
      flash_error('Please configure the path to SVN executable before prior to adding a repository');
      $this->redirectTo('admin_source');
    } // if
    
    $repository_data = $this->request->post('repository');
    if(!is_array($repository_data)) {
      $repository_data = array(
      'visibility'       => $this->active_project->getDefaultVisibility(),
      );
    } // if

    if ($this->request->isSubmitted()) {
      $repository_data['name'] = trim($repository_data['name']) == '' ? $repository_data['url'] : $repository_data['name'];
      $this->active_repository->setAttributes($repository_data);
      $this->active_repository->setProjectId($this->active_project->getId());
      $this->active_repository->setCreatedBy($this->logged_user);
      $this->active_repository->setState(STATE_VISIBLE);
      
      $this->active_repository->loadEngine($this->active_repository->getRepositoryType());
    
      $this->repository_engine = new RepositoryEngine($this->active_repository);
      $this->repository_engine->triggerred_by_handler = true;
    
      $result = $this->repository_engine->testRepositoryConnection();
      if ($result === true) {
        $save = $this->active_repository->save();
        if ($save && !is_error($save)) {
          flash_success(lang('Project repository &quot;:name&quot; has been added successfully'), array('name'=>$this->active_repository->getName()));
          $this->redirectToUrl(source_module_url($this->active_project));
        } else {
          $this->smarty->assign('errors', $save);
        } //if
      }
      else {
        $errors = new ValidationErrors();
        $errors->addError(lang('Failed to connect to repository: :message', array('message'=>$result)));
        $this->smarty->assign('errors', $errors);
      } // if
      
    } // if
    
    $test_connection_url = assemble_url('repository_test_connection', array('project_id'=>$this->active_project->getId()));
    js_assign('repository_test_connection_url', $test_connection_url);

    $this->smarty->assign(array(
      'types'                 => $this->active_repository->types,
      'update_types'          => $this->active_repository->update_types,
      'repository_add_url'    => assemble_url('repository_add', array('project_id'=>$this->active_project->getId())),
      'repository_data'       => $repository_data,
      'disable_url_and_type'  => false,
      'aid_url'               => lang('Please enter the root path to the repository'),
      'aid_engine'            => ''
    ));
  } // add a repository


  /**
   * Edit repository
   *
   * @param null
   * @return void
   */
  function edit() {
    if(!$this->active_repository->canEdit($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    $repository_data = $this->request->post('repository');
    if (!is_array($repository_data)) {
      $repository_data = array(
      'name'            => $this->active_repository->getName(),
      'url'             => $this->active_repository->getUrl(),
      'username'        => $this->active_repository->getUsername(),
      'password'        => $this->active_repository->getPassword(),
      'repositorytype'  => $this->active_repository->getRepositoryType(),
      'updatetype'      => $this->active_repository->getUpdateType(),
      'visibility'      => $this->active_repository->getVisibility()
      );
    }

    if ($this->request->isSubmitted()) {
      db_begin_work();
      $this->active_repository->setAttributes($repository_data);
      
      $this->active_repository->loadEngine($this->active_repository->getRepositoryType());
    
      $this->repository_engine = new RepositoryEngine($this->active_repository);
      $this->repository_engine->triggerred_by_handler = true;
    
      $result = $this->repository_engine->testRepositoryConnection();
      if ($result === true) {
        $save = $this->active_repository->save();
        if ($save && !is_error($save)) {
          db_commit();
          flash_success(lang('Repository has been successfully updated'));
          $this->redirectToUrl($this->active_repository->getHistoryUrl());
        } else {
          db_rollback();
          $this->smarty->assign('errors', $save);
        } //if
      }
      else {
        db_rollback();
        $errors = new ValidationErrors();
        $errors->addError(lang('Failed to connect to repository: :message', array('message'=>$result)));
        $this->smarty->assign('errors', $errors);
      } // if

    } // if
    
    js_assign('repository_test_connection_url', assemble_url('repository_test_connection', array('project_id'=>$this->active_project->getId())));

    $this->smarty->assign(array(
      'types'               => $this->active_repository->types, // visak!
      'update_types'        => $this->active_repository->update_types, // visak!
      'repository_data'     => $repository_data,
      'active_repository'   => $this->active_repository,
      'disable_url_and_type'  => true,
      'aid_url'               => lang('The path to the existing repository cannot be changed'),
      'aid_engine'            => lang('Repository type cannot be changed'),
    ));

  } // edit repository

  /**
   * Delete repository
   * 
   * @param null
   * @return void
   *
   */
  function delete() {
    if (!$this->active_repository->canEdit($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    $delete = $this->active_repository->delete(true);
    
    CommitProjectObjects::delete("repository_id = '".$this->active_repository->getId()."'");
    SourceUsers::delete("repository_id = '".$this->active_repository->getId()."'");
    
    if ($delete && !is_error($delete)) {
      flash_success('Repository has been successfully deleted');
      $this->redirectToUrl(source_module_url($this->active_project));
    } else {
      $this->smarty->assign('errors', $delete);
    } // if
  } // delete

  /**
   * Update a repository
   *
   * @param null
   * @return void
   */
  function update() {
    if (!$this->active_repository->canEdit($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    $last_commit = $this->active_repository->getLastCommit();
    $revision_to = !instance_of($last_commit, 'Commit') ? 1 : $last_commit->getRevision();
    $latest_revision = !instance_of($last_commit, 'Commit') ? 0 : $last_commit->getRevision();
    $head_revision = $this->repository_engine->getHeadRevision($this->request->isAsyncCall());
    $bulk_update = array_var($_GET, 'bulk') == '1';
    
    $this->repository_engine->ignore_missing_path_errors = true;

    // simple mass update
    if ($this->request->isAsyncCall() == false && $bulk_update) {
      if (is_null($this->repository_engine->error)) {
        $logs = $this->repository_engine->getLogs($revision_to);

        // Loop through array of logs and prepare data for inserting into project_objects table
        if (is_foreachable($logs['data'])) {
          $this->active_repository->update($logs['data']);
        } // if

        if ($logs['total'] > 0) {
          $this->active_repository->sendToSubscribers($logs['total'], $this->repository_engine);
          $this->active_repository->createActivityLog($logs['total']);
          flash_success(lang('Update successfully performed with :total_commits new history entries added.'), array('total_commits'=>$logs['total']));
        } else {
          flash_success(lang('Repository is already up-to-date'));
        } // if
      }
      else {
        flash_error($this->repository_engine->error);
      } // if

      $this->redirectToReferer(source_module_url($this->active_project));
    } // if

    // async
    else {
      if (!is_null($this->repository_engine->error)) {
        die($this->repository_engine->error);
      } // if
      
      $revision = intval(array_var($_GET, 'r'));
      $notify_subscribers = array_var($_GET, 'notify');
      
      if ($revision > 0) {        
        $log = $this->repository_engine->getLogs($revision, null);
        if (is_null($this->repository_engine->error)) {
          $this->active_repository->update($log['data']);
          die('success');
        } else {
          die($this->repository_engine->error);
        } // if
      } // if
      
      
      if ($notify_subscribers) {
        $total_commits = $notify_subscribers;
        
        $this->active_repository->sendToSubscribers($total_commits, $this->repository_engine);
        $this->active_repository->createActivityLog($total_commits);
        
        die('success');
      } // if

      $uptodate = intval($head_revision == $latest_revision);
      $this->smarty->assign(array(
        'uptodate'      => $uptodate,
        'head_revision' => $head_revision,
        'last_revision' => $latest_revision,
        'repository_update_url'    => str_replace('/', '\/', $this->active_repository->getUpdateUrl()),
        'indicator_ok'  => ASSETS_URL.'/images/ok_indicator.gif',
      ));
    }
  } // update repository
  
  
  /**
   * Manage repository users
   *
   * @param void
   * @return null
   */
  function repository_users() {
    if ($this->active_repository->isNew()) {
      flash_error('Repository does not exist');
      $this->redirectToReferer(SOURCE_MODULE_PATH);
    } // if
    
    if (!$this->active_repository->canEdit($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN);
    } // if
    
    $this->wireframe->addPageAction(lang('Browse repository'), $this->active_repository->getBrowseUrl(), null);
    $this->wireframe->addPageAction(lang('Commit History'), $this->active_repository->getHistoryUrl());
    
    $source_users = SourceUsers::findByRepository($this->active_repository);
    $distinct_repository_users = $this->active_repository->getDistinctUsers();
    
    // loop through already mapped users and remove them from repository users
    foreach ($source_users as $source_user) {
      $mapped_user_key = array_search($source_user->getRepositoryUser(), $distinct_repository_users);
    	if ($mapped_user_key !== false) {
    	  unset($distinct_repository_users[$mapped_user_key]);
    	} // if
    } // foreach
    
    $this->smarty->assign(array(
      'source_users' => $source_users,
      'repository_users' => $distinct_repository_users,
      'system_users' => ProjectUsers::findByProject($this->active_project),
      'repository_user_add_url' => assemble_url('repository_user_add', array('project_id' => $this->active_project->getId(), 'repository_id' => $this->active_repository->getId()))
    ));
  } // repository_users
  
  /**
   * Add mapping repository_user -> activecollab user
   *
   * @param void
   * @return null
   */
  function repository_user_add() {
    if (!$this->active_repository->canEdit($this->logged_user)) {
      $this->httpError(HTTP_ERR_FORBIDDEN, null, true);
    } // if
    
    if (!$this->request->isSubmitted()) {
      $this->httpError(HTTP_ERR_BAD_REQUEST, null, true);
    } //if
    
    if (!$this->request->isAsyncCall()) {
      $this->httpError(HTTP_ERR_BAD_REQUEST, null, true);
    } //if

    $source_user = new SourceUser();

    $source_user->setRepositoryId($this->active_repository->getId());
    $source_user->setRepositoryUser($this->request->post('repository_user'));
    $source_user->setUserId($this->request->post('user_id'));
    
    // validation is moved here because the management form is not on separate page
    if (!$source_user->validatePresenceOf('user_id')) {
      die('error');
    } // if
    
    $save = $source_user->save();
    
    if ($save && !is_error($save)) {
      $source_user->setSystemUser();
      $this->smarty->assign('source_user', $source_user);
      $this->smarty->display(get_template_path('_repository_user_row', 'repository', SOURCE_MODULE));
      die();
    } else {
      die('error');
    } // if
  } // repository_user_add
  
  /**
   * Delete user mapping
   *
   * @param void
   * @return null
   */
  function repository_user_delete() {
    if (!$this->request->isAsyncCall()) {
      $this->httpError(HTTP_BAD_REQUEST, null, true);
    } // if
    
    if (!$this->request->isSubmitted()) {
      $this->httpError(HTTP_BAD_REQUEST, null, true);
    } // if
    
    $repository_user = array_var($_POST, 'repository_user');
    $source_user = SourceUsers::findByRepositoryUser($repository_user, $this->active_repository->getId());
    if (!instance_of($source_user, 'SourceUser')) {
      die('false');
    } // if
    
    $deleted = $source_user->delete();
    if ($deleted && !is_error($deleted)) {
      die('true');
    }
    
  } // repository_user_delete
  
  
  /**
   * Test repository connection
   *
   * @param null
   * @return void
   */
  function test_repository_connection() {
    $this->active_repository->setUrl(array_var($_GET, 'url'));
    $this->active_repository->setUsername(array_var($_GET, 'user'));
    $this->active_repository->setPassword(array_var($_GET, 'pass'));
    $this->active_repository->setRepositoryType(array_var($_GET, 'engine'));
    
    if (!$this->active_repository->loadEngine($this->active_repository->getRepositoryType())) {
      die(lang('Failed to load repository engine'));
    }
    
    $this->repository_engine = new RepositoryEngine($this->active_repository);
    $this->repository_engine->triggerred_by_handler = true;
    
    $result = $this->repository_engine->testRepositoryConnection();
    if ($result !== true) {
      die($result);
    }
    else {
      die('ok');
    } // if
    
  } // test_repository_connection

}

?>