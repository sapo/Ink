<?php

  /**
   * Repository class
   * 
   * @package activeCollab.modules.source
   */
  class Repository extends ProjectObject {
    
    /**
     * Permission name
     * 
     * @var string
     */
    var $permission_name = 'repository';
    
    /**
     * Project tab (compatibility with rel 1.1)
     * 
     * @var string
     */
    var $project_tab = 'source';
  
    /**
     * Log object activities
     *
     * @var booelan
     */
    var $log_activities = false;
    
    /**
     * Repositories can have subscribers
     *
     * @var boolean
     */
    var $can_have_subscribers = true;
    
    /**
     * Name of the route used for portal view URL
     *
     * @var string
     */
    var $portal_view_route_name = 'portal_repository';
    
    /**
     * Name of the object ID variable used alongside project_id when URL-s are
     * generated (eg. comment_id, task_id, message_category_id etc)
     *
     * @var string
     */
    var $object_id_param_name = 'repository_id';
  
    /**
     * Fields used by this module
     *
     * @var array
     */
    var $fields = array(
    'id',
    'type', 'module',
    'project_id',
    'name',
    'body',
    'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
    'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email',
    'varchar_field_1', // username
    'varchar_field_2', // password
    'text_field_1', // repository url
    'text_field_2', // serialized info for repository commit histogram
    'integer_field_1', // repository types
    'integer_field_2', // repository update type
    'state', 'visibility',
    );
  
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
    'root_path' => 'body',
    'username' => 'varchar_field_1',
    'password' => 'varchar_field_2',
    'url' => 'text_field_1',
    'repositorytype' => 'integer_field_1',
    'updatetype' => 'integer_field_2',
    'graph' => 'text_field_2',
    );
  
  
    /**
     * List of commits
     *
     * @var mixed
     */
    var $commits = null;
  
    /**
     * Last commit info
     *
     * @var mixed
     */
    var $last_commit = null;
  
    /**
     * We do not need protected vars here
     *
     * @var array
     */
    var $protect = array();
  
    /**
     * List of supported source version systems
     *
     * @var array
     */
    var $types = array();
  
    /**
     * Repository update types
     * 
     * Values are placed into __construct and variable is populated there
     * because calling lang() function is not possible when defining class variables
     *
     * @var array
     */
    var $update_types = array();
    
    /**
     * Array of mapped repository users
     *
     * @var array
     */
    var $mapped_users = array();
  
    /**
     * Construct a new repository
     *
     * @param int $id
     */
    function __construct() {
      parent::__construct();
  
      $this->setModule(SOURCE_MODULE);
  
      $this->update_types = source_module_update_types();
      $this->types = source_module_types();
    } // __construct
    
    /**
     * Returns true if $user can create a new repository in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canAdd($user, $project) {
      return ProjectObject::canAdd($user, $project, 'repository');
    } // canAdd
  
    /**
     * Use specific version control engine
     *
     * @param string $engine
     */
    function loadEngine() {
      $engine = strtolower($this->types[$this->getRepositoryType()]);
  
      if (is_file(SOURCE_MODULE_PATH.'/engines/'.$engine.'.class.php')) {
        require_once(SOURCE_MODULE_PATH.'/engines/'.$engine.'.class.php');
        return true;
      }
      
      return false;
    } // load engine
    
    /**
     * Get distinct list of users from repository commits
     *
     * @param void
     * @return array
     */
    function getDistinctUsers() {
      $users = array();
      $repository_users = db_execute("SELECT DISTINCT created_by_name FROM ".TABLE_PREFIX."project_objects WHERE parent_id = ".$this->getId()." ORDER BY created_by_name ASC");
      if (is_foreachable($repository_users)) {
        foreach ($repository_users as $repository_user) {
        	$users[] = $repository_user['created_by_name'];
        } // foreach
      } // if
      
      return $users;
    } // getDistinctUsers
    
    /**
     * Create update log
     *
     * @param integer $log_items
     * @return void
     */
    function createActivityLog($log_items) {
      $log = new RepositoryUpdateActivityLog();
      return $log->log($this, $this->getCreatedBy(), lang(':total_commits commits added', array('total_commits' => $log_items)));
    } // create activity log
  
    /**
     * Make the tree navigation linked
     *
     * @param integer $revision
     * @param string $path
     * @return string
     */
    function linkify($revision, $path) {
      $items = explode("/",$revision.$path);
      $linked = array();
      $current_dir = "";
  
      $delimiter = define(PATH_INFO_THROUGH_QUERY_STRING) && PATH_INFO_THROUGH_QUERY_STRING ? '?' : '&';
      foreach ($items as $key=>$item) {
        if ($key == 0) {
          $linked[$key] = '<a href="'.$this->active_repository->getBrowseurl(). $delimiter . 'r='.$revision.'">'.$revision.'</a>';
        }
        elseif (!isset($items[$key+1])) {
          $linked[$key] = $item;
        }
        else {
          $current_dir .= '/'.$item;
          $linked[$key] = '<a href="'.$this->active_repository->getBrowseUrl(). $delimiter . 'path='.$current_dir.'&amp;r='.$revision.'">'.$item.'</a>';
        }
      }
  
      return "/".implode("/", $linked);
    } // linkify navigation
  
    
    /**
     * Update repository with new commits
     *
     * @param array $logs
     */
    function update($logs) {
      $query_insert = "INSERT INTO ".TABLE_PREFIX."project_objects (`type`,`module`,`project_id`,`parent_id`,`parent_type`, `body`, `integer_field_1`, `text_field_1`, `created_by_name`, `created_on`, `state`, `visibility`) VALUES ";
  
      if (is_foreachable($logs)) {
        foreach ($logs as $data) {
          $query = $query_insert . implode(',',$data);
          db_execute($query) or die(mysql_error().'<br/>'.$query);
        } // foreach
      } // if
    } // update repository
    
  
    /**
     * Get information about latest commit
     *
     * @param null
     * @return Commit
     */
    function getLastCommit() {
      return Repositories::find(array(
      'conditions'  => array('parent_id = ? AND `type` = ?', $this->getId(), 'Commit'),
      'order'       => 'integer_field_1 DESC',
      'one'         => true
      ));
    } // get last commit
    
    
    /**
     * Get recent activity for repositiry graph at module home page
     *
     * @param null
     * @return array
     */
    function getRecentActivity() {
      $cached_data = $this->getGraphData();
      $latest_commit = $this->getLastCommit();
      if (!instance_of($latest_commit, 'Commit')) {
        return null;
      } // if
      $latest_revision = $latest_commit->getRevision();
      
      $cache_id = date('m-d-Y').'_'.$latest_revision;
      
      if (isset($cached_data['logs']) && is_array($cached_data['logs']) && $cached_data['cache_id'] == $cache_id) {
        $graph_data = $cached_data['logs'];
      }
      else {
        $graph_data = Commits::getRecentActivity($this);
        $this->setGraphData(array(
          'logs' => $graph_data,
          'cache_id' => $cache_id,
        ));
          
        $this->save();
      }
      
      return $graph_data;
    } // get recent activity
    
    /**
     * Get mapped user
     *
     * @param string $repository_user
     * return mixed
     */
    function getMappedUser($repository_user) {
      if (!is_foreachable($this->mapped_users)) {
        $this->mapped_users = SourceUsers::findByRepository($this);
      } // if
      
      if (isset($this->mapped_users[$repository_user]) && instance_of($this->mapped_users[$repository_user], 'SourceUser')) {
        $source_user = $this->mapped_users[$repository_user];
        if (instance_of($source_user->system_user, 'User')) {
          return $source_user->system_user;
        } // if
      } // if
      
      return new AnonymousUser($repository_user, 'nobody@site.com');
    }
    
    /**
     * Set repository URL
     *
     * @param string $value
     * @return boolean
     */
    function setUrl($value) {
      return $this->setFieldValue('text_field_1', $value);
    } // setUrl
  
    /**
     * Get repository URL
     *
     * @return string
     */
    function getUrl() {
      return str_replace(' ', '%20', $this->getFieldValue('text_field_1'));
    } // getUrl
    
    /**
     * Set data for repository graph
     *
     * @param array $value
     * @return boolean
     */
    function setGraphData($value) {
      return $this->setFieldValue('text_field_2', serialize($value));
    } // setGraphData
    
    /**
     * Get data for repository graph
     *
     * @return array
     */
    function getGraphData() {
      return unserialize($this->getFieldValue('text_field_2'));
    } // getGraphData
  
    /**
     * Get repository username
     *
     * @return string
     */
    function getUsername() {
      return $this->getFieldValue('varchar_field_1');
    } // getUsername
  
    /**
     * Set repository username
     *
     * @param string $value
     * @return boolean
     */
    function setUsername($value) {
      return $this->setFieldValue('varchar_field_1', $value);
    } // setUsername
  
    /**
     * Get repository password
     *
     * @return string
     */
    function getPassword() {
      return $this->getFieldValue('varchar_field_2');
    } // getPassword
  
    /**
     * Set repository password
     *
     * @param string $value
     * @return boolean
     */
    function setPassword($value) {
      return $this->setFieldValue('varchar_field_2', $value);
    } // setPassword
  
    /**
     * Get repository type
     *
     * @return int
     */
    function getRepositoryType() {
      return $this->getFieldValue('integer_field_1');
    } // getRepositoryType
  
    /**
     * Set repository type
     *
     * @param int $value
     * @return boolean
     */
    function setRepositoryType($value) {
      return $this->setFieldValue('integer_field_1', $value);
    } // setRepositoryType
  
    /**
     * Get update type
     *
     * @return int
     */
    function getUpdateType() {
      return $this->getFieldValue('integer_field_2');
    } // getUpdateType
  
    /**
     * Set update type
     *
     * @param int $value
     * @return boolean
     */
    function setUpdateType($value) {
      return $this->setFieldValue('integer_field_2', $value);
    } // setUpdateType
  
    /**
     * Get edit URL
     *
     * @return string
     */
    function getEditUrl() {
      return assemble_url('repository_edit', array('repository_id'=>$this->getId(), 'project_id'=>$this->getProjectId()));
    } // getEditUrl
  
    /**
     * Get URL for file revision compare
     *
     * @param mixed $revision
     * @param string $path
     * @return string
     */
    function getFileCompareUrl($revision, $path, $peg_revision = null) {
      $params = array('repository_id'=>$this->getId(), 'project_id'=>$this->getProjectId());
      
      if($revision !== null) {
        if (instance_of($revision, 'Commit')) {
          $params['r'] = $revision->getRevision();
        } else {
          $params['r'] = $revision;
        } // if
      } // if
      
      if ($peg_revision !== null) {
        $params['peg'] = $peg_revision;
      } // if
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      return assemble_url('repository_compare',$params);
    } // get compare URL
  
  
    /**
     * Get file download URL
     *
     * @param mixed $revision
     * @param string $path
     * @return string
     */
    function getFileDownloadUrl($revision, $path, $peg_revision = null) {
      $params = array('repository_id'=>$this->getId(), 'project_id'=>$this->getProjectId());
      
      if($revision !== null) {
        if (instance_of($revision, 'Commit')) {
          $params['r'] = $revision->getRevision();
        } else {
          $params['r'] = $revision;
        } // if
      } // if
      
      if ($peg_revision !== null) {
        $params['peg'] = $peg_revision;
      } // if
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      return assemble_url('repository_file_download', $params);
    } // get file download URL
  
    /**
     * Get file history URL
     *
     * @param mixed $revision
     * @param string $path
     * @return string
     */
    function getFileHistoryUrl($revision, $path, $peg_revision) {
      $params = array('repository_id'=>$this->getId(), 'project_id'=>$this->getProjectId());
      
      if($revision !== null) {
        if (instance_of($revision, 'Commit')) {
          $params['r'] = $revision->getRevision();
        } else {
          $params['r'] = $revision;
        } // if
      } // if
      
      if ($peg_revision !== null) {
        $params['peg'] = $peg_revision;
      } // if
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      return assemble_url('repository_file_history',$params);
    } // file history URL
  
  
    /**
     * Get view URL
     *
     * @return string
     */
    function getViewUrl() {
      return $this->getHistoryUrl();
    } // getViewUrl
  
    /**
     * Get repository history URL
     *
     * @param null
     * @return string
     */
    function getHistoryUrl($commit_author = null) {
      $params = array('repository_id'=>$this->getId(),'project_id'=>$this->getProjectId());
      
      if (!is_null($commit_author)) {
        $params['filter_by_author'] = $commit_author;
      } // if
      
      return assemble_url('repository_history', $params);
    } // get history URL
  
    /**
     * Get update repository URL
     *
     * @param null
     * @return string
     */
    function getUpdateUrl() {
      return assemble_url('repository_update', array('repository_id'=>$this->getId(), 'project_id'=>$this->getProjectId()));
    } // get update url
  
    /**
     * Get the url for fetching item info
     *
     * @param null
     * @return string
     */
    function getItemInfoUrl($revision = null, $path = null, $peg_revision = null) {
      $params = array('repository_id'=>$this->getId(), 'project_id'=>$this->getProjectId());
      
      if($revision !== null) {
        if (instance_of($revision, 'Commit')) {
          $params['r'] = $revision->getRevision();
        } else {
          $params['r'] = $revision;
        } // if
      } // if
      
      if (!is_null($peg_revision)) {
        $params['peg'] = $peg_revision;
      } // if
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      return assemble_url('repository_item_info', $params);
    } // getItemInfoUrl
    
    /**
     * Get browse URL
     *
     * @param null
     * @return string
     */
    function getBrowseUrl($revision = null, $path = null, $peg_revision = null) {
      $params = array('repository_id'=>$this->getId(), 'project_id'=>$this->getProjectId());
      
      if($revision !== null) {
        if (instance_of($revision, 'Commit')) {
          $params['r'] = $revision->getRevision();
        } else {
          $params['r'] = $revision;
        } // if
      } // if
      
      if ($peg_revision !== null) {
        $params['peg'] = $peg_revision;
      } // if
      
      if($path !== null) {
        $params['path'] = $path;
      } // if
      
      return assemble_url('repository_browse', $params);
    } // get browse url
  
    /**
     * Return commit details URL
     *
     * @param int $revision
     * @return string
     */
    function getCommitUrl($revision) {
      return assemble_url('repository_commit', array('repository_id'=>$this->getId(), 'project_id'=>$this->getProjectId(), 'r'=> $revision));
    } // get commit url
    
    /**
     * Send notification to subscribers
     *
     * @param int $logs_count
     */
    function sendToSubscribers($logs_count, &$repository_engine, $params = array(), $exclude = null) {
      if (!is_array($params)) {
        $params = array();
      } // if
      
      $params['commit_count'] = $logs_count;
      $params['commits_body'] = "";
      
      if ($logs_count <= 10) {        
        $commits = Commits::find(array(
          'conditions' => "parent_id = '".$this->getId()."'",
          'order' => 'created_on DESC',
          'limit' => $logs_count
        ));
        
        if (is_foreachable($commits)) {
          foreach ($commits as $key=>$commit) {
            $paths = $commit->getPaths();
            
            $params['commits_body'] .= "<hr/>";
            $params['commits_body'] .= "<p><a href=\"".$commit->getViewUrl()."\">".lang("<b>Commit #:revision</b>", array('revision' => $commit->getRevision(), false)). '</a> '.lang('by :author', array('author' => $commit->getAuthor()), false)."</p>\n";
            $params['commits_body'] .= nl2br(trim($commit->getMessage()))."\n";
            $params['commits_body'] .= $repository_engine->getCommitTemplateBit($paths);
          } // foreach
          
          $params['commits_body'] .= "<hr/>";
        } // if
        
      } // if
      
      parent::sendToSubscribers("source/repository_updated", $params, get_logged_user_id());
    } // sendToSubscribers
      
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Repository has to have a name'), 'name');
      } // if
  
      if(!$this->validatePresenceOf('url')) {
        $errors->addError(lang('You need to enter repository URL'), 'url');
      } // if
  
      if(!$this->validatePresenceOf('username')) {
        $errors->addError(lang('You need to enter repository username'), 'username');
      } // if
  
      if(!$this->validatePresenceOf('password')) {
        $errors->addError(lang('You need to enter repository password'), 'password');
      } // if
  
      parent::validate($errors, true);
    } // validate
    
    // ---------------------------------------------------
    //  Portal methods
    // ---------------------------------------------------
    
    /**
     * Return portal repository view URL
     *
     * @param Portal $portal
     * @param integer $page
     * @return string
     */
    function getPortalViewUrl($portal, $page = null) {
    	$params = $page === nul ? null : array('page' => $page);
    	return parent::getPortalViewUrl($portal, $page);
    } // getPortalViewUrl
    
    /**
     * Return portal repository browse URL
     *
     * @param Portal $portal
     * @param mixed $revision
     * @param string $path
     * @return string
     */
    function getPortalBrowseUrl($portal, $revision = null, $path = null) {
    	if(!instance_of($portal, 'Portal')) {
    		return '';
    	} // if
    	
    	$params = array(
    		'portal_name'   => $portal->getSlug(),
    		'repository_id' => $this->getId()
    	);
    	
    	if($revision !== null) {
    		if(instance_of($revision, 'Commit')) {
    			$params['r'] = $revision->getRevision();
    		} else {
    			$params['r'] = $revision;
    		} // if
    	} // if
    	
    	if($path !== null) {
    		$params['path'] = $path;
    	} // if
    	
    	return assemble_url('portal_repository_browse', $params);
    } // getPortalBrowseUrl
    
    /**
     * Return fetching portal item info URL
     *
     * @param Portal $portal
     * @param integer $revision
     * @param string $path
     * @return string
     */
    function getPortalItemInfoUrl($portal, $revision = null, $path = null) {
    	$params = array('portal_name' => $portal->getSlug(), 'repository_id' => $this->getId());
    	
    	if($revision !== null) {
    		if(instance_of($revision, 'Commit')) {
    			$params['r'] = $revision->getRevision();
    		} else {
    			$params['r'] = $revision;
    		} // if
    	} // if
    	
    	if($path !== null) {
    		$params['path'] = $path;
    	} // if
    	
    	return assemble_url('portal_repository_item_info', $params);
    } // getPortalItemInfoUrl
    
    /**
     * Return portal file history URL
     *
     * @param Portal $portal
     * @param integer $revision
     * @param string $path
     * @return string
     */
    function getPortalFileHistoryUrl($portal, $revision = null, $path = null) {
    	$params = array('portal_name' => $portal->getSlug(), 'repository_id' => $this->getId());
    	
    	if($revision !== null) {
    		if(instance_of($revision, 'Commit')) {
    			$params['r'] = $revision->getRevision();
    		} else {
    			$params['r'] = $revision;
    		} // if
    	} // if
    	
    	if($path !== null) {
    		$params['path'] = $path;
    	} // if
    	
    	return assemble_url('portal_repository_file_history', $params);
    } // getPortalFileHistoryUrl
    
    /**
     * Return portal file revision compare URL
     *
     * @param Portal $portal
     * @param integer $revision
     * @param string $path
     * @return string
     */
    function getPortalFileCompareUrl($portal, $revision = null, $path = null) {
    	$params = array('portal_name' => $portal->getSlug(), 'repository_id' => $this->getId());
    	
    	if($revision !== null) {
    		if(instance_of($revision, 'Commit')) {
    			$params['r'] = $revision->getRevision();
    		} else {
    			$params['r'] = $revision;
    		} // if
    	} // if
    	
    	if($path !== null) {
    		$params['path'] = $path;
    	} // if
    	
    	return assemble_url('portal_repository_compare', $params);
    } // getPortalFileCompareUrl
    
    /**
     * Return portal file download URL
     *
     * @param Portal $portal
     * @param integer $revision
     * @param string $path
     * @return string
     */
    function getPortalFileDownloadUrl($portal, $revision = null, $path = null) {
    	$params = array('portal_name' => $portal->getSlug(), 'repository_id' => $this->getId());
    	
    	if($revision !== null) {
    		if(instance_of($revision, 'Commit')) {
    			$params['r'] = $revision->getRevision();
    		} else {
    			$params['r'] = $revision;
    		} // if
    	} // if
    	
    	if($path !== null) {
    		$params['path'] = $path;
    	} // if
    	
    	return assemble_url('portal_repository_file_download', $params);
    } // getPortalFileDownloadUrl
    
    /**
     * Return portal commit details URL
     *
     * @param Portal $portal
     * @param integer $revision
     * @return string
     */
    function getPortalCommitUrl($portal, $revision) {
    	return assemble_url('portal_repository_commit', array('portal_name' => $portal->getSlug(), 'repository_id' => $this->getId(), 'r' => $revision));
    } // getPortalCommitUrl
    
    /**
     * Make the tree navigation linked to portal
     *
     * @param Portal $portal
     * @param integer $revision
     * @param string $path
     * @return string
     */
    function portal_linkify($portal, $revision, $path) {
    	$items = explode("/", $revision.$path);
    	$linked = array();
    	$current_dir = "";
    	
    	$delimiter = define(PATH_INFO_THROUGH_QUERY_STRING) && PATH_INFO_THROUGH_QUERY_STRING ? '?' : '&';
    	foreach($items as $key => $item) {
    		if($key == 0) {
    			$linked[$key] = '<a href="'.$this->active_repository->getPortalBrowseUrl($portal).$delimiter.'r='.$revision.'">'.$revision.'</a>';
    		} elseif(!isset($items[$key + 1])) {
    			$linked[$key] = $item;
    		} else {
    			$current_dir .= '/'.$item;
    			$linked[$key] = '<a href="'.$this->active_repository->getPortalBrowseUrl($portal).$delimiter.'path='.$current_dir.'&amp;r='.$revision.'">'.$item.'</a>';
    		} // if
    	} // foreach
    	
    	return "/".implode("/", $linked);
    } // portal_linkify
  
  }

?>