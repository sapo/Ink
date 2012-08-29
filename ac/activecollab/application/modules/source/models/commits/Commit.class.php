<?php

/**
   * Commit record class
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
class Commit extends ProjectObject {
  
  /**
   * Permission name
   * 
   * @var string
   */
  var $permission_name = 'repository';

  /**
   * Project tab
   *
   * @var string
   */
  var $project_tab = 'source';
  
  /**
   * Total paths affected
   *
   * @var int
   */
  var $total_paths = 0;

  /**
   * Define fields used by this project object
   *
   * @var array
   */
  var $fields = array(
  'id',
  'type', 'module',
  'project_id', 'parent_id', 'parent_type',
  'body',
  'created_on', 'created_by_name', 'created_by_email',
  'state',
  'visibility',
  'integer_field_1', // revision
  'text_field_1', // paths
  'text_field_2' // diff changes
  );

  /**
   * Field map
   *
   * @var array
   */
  var $field_map = array(
  'revision'  => 'integer_field_1',
  'paths'     => 'text_field_1',
  'diff'      => 'text_field_2'
  );

  
  /**
   * Repository that commit belongs to
   *
   * @var Repository
   */
  var $repository = null;

  /**
   * Construct a new ticket
   *
   * @param mixed $id
   * @return Ticket
   */
  function __construct($id = null) {
    parent::__construct();
    $this->setModule(SOURCE_MODULE);
  } // __construct
  
  
  /**
   * Find project objects in commit message, make them links and
   * save the relations to database
   *
   * @param string $commit_message
   * @return string
   */
  function analyze_message($commit_message, $commit_author, $revision, $repository, $project) {

    $pattern = '/((complete[d]*)[\s]+)?(ticket|milestone|discussion|task)[s]*[\s]+[#]*\d+/i';
     
    if (preg_match_all($pattern, $commit_message, $matches)) {
      $i = 0;
      $search = array();
      $replace = array();
      
      $matches_unique = array_unique($matches['0']);
      
      foreach ($matches_unique as $key => $match) {
        $match_data = preg_split('/[\s,]+/', $match, null, PREG_SPLIT_NO_EMPTY);
        
        // check if the object got completed by this commit
        $object_completed = false;
        if (strpos(strtolower($match_data['0']), 'complete') !== false) {
          $object_completed = true;
          unset($match_data['0']);
          $match_data = array_values($match_data);
        } // if
        
        $object_class_name = $match_data['0'];
      	$module_name = Inflector::pluralize($object_class_name);
      	$object_id = trim($match_data['1'], '#');
      	$search[$i] = $match;
      	
      	if (class_exists($module_name) && class_exists($object_class_name)) {
      	  $object = null;
      	  
      	  switch (strtolower($module_name)) {
      	  	case 'tickets':
      	  	  $object = Tickets::findByTicketId($project, $object_id);
      	  		break;
      	  	case 'discussions':
      	  	  $object = Discussions::findById($object_id);
      	  	  break;
      	  	case 'milestones':
      	  	  $object = Milestones::findById($object_id);
      	  	  break;
      	  	case 'tasks' :
      	  	  $object = Tasks::findById($object_id);
      	  	  break;
      	  } // switch
      	  
      	  if (instance_of($object, $object_class_name)) {
      	    $link_already_created = CommitProjectObjects::count("object_id = '".$object->getId()."' AND revision = '$revision'") > 0;
      	    
      	    if (!$link_already_created) {
      	      $comit_project_object = new CommitProjectObject();
      	      $comit_project_object->setProjectId($object->getProjectId());
      	      $comit_project_object->setObjectId($object->getId());
      	      $comit_project_object->setObjectType(ucfirst($object_class_name));
      	      $comit_project_object->setRepositoryId($repository->getId());
      	      $comit_project_object->setRevision($revision);
      	      
      	      db_begin_work();
      	      $save = $comit_project_object->save();
      	      if ($save && !is_error($save)) {
      	        db_commit();
      	      } else {
      	        db_rollback();
      	      } // if save
      	      
      	    } // if
      	    
      	    $replace[$i] = ($object_completed ? 'Completed ' : '') . '<a href="'.$object->getViewUrl().'">'.$match_data['0'].' '.$match_data['1'].'</a>';
      	    
      	    // set the object as completed
      	    if ($object_completed && !instance_of($object, 'Discussion')) {
      	      $completed_by = $repository->getMappedUser($commit_author);
      	      $object->complete($completed_by);
      	    } // if
      	  }
      	  else {
      	    $replace[$i] = ($object_completed ? 'Completed ' : '') . '<a href="#" class="project_object_missing" title="'.lang('Project object does not exist in this project').'">'.$match_data['0'].' '.$match_data['1'].'</a>';
      	  } // if instance_of
      	  
      	  $i++;
      	} // if module loaded
      } // foreach
      
      return str_ireplace($search, $replace, htmlspecialchars($commit_message)); // linkify
      
    } // if preg_match
      
    return $commit_message;
  } // get_project_objects
  
  
  /**
   * Return count of affected paths
   *
   * @return integer
   */
  function countPaths() {
    $paths = $this->getPaths();
    return (int) count($pats);
  } // countPaths
  
  /**
   * Get Author
   *
   * @param Repository $repository
   * @return string
   */
  function getAuthor($repository = null) {
    if (!instance_of($repository, 'Repository') || $repository->isNew()) {
      $repository = Repositories::findById($this->getParentId());
    } // if
    
    if (!is_foreachable($repository->mapped_users)) {
      $repository->mapped_users = SourceUsers::findByRepository($repository);
    } // if
    
    if (isset($repository->mapped_users[$this->getCreatedByName()]) && instance_of($repository->mapped_users[$this->getCreatedByName()], 'SourceUser')) {
      $source_user = $repository->mapped_users[$this->getCreatedByName()];
      $system_user = $source_user->system_user;
      if (instance_of($system_user, 'User')) {
        return '<a href="'.$system_user->getViewUrl().'">'.$system_user->getDisplayName(true).'</a>';
      } // if
    } // if
    
    return $this->getCreatedByName();
  } // getAuthor
  
  /**
   * Return commit author (adapted for portal view)
   *
   * @param Repository $repository
   * @return string
   */
  function getPortalAuthor($repository = null) {
  	if(!instance_of($repository, 'Repository') || $repository->isNew()) {
  		$repository = Repositories::findById($this->getParentId());
  		$repository->mapped_users = SourceUsers::findByRepository($repository);
  	} // if
  	
  	if(isset($repository->mapped_users[$this->getCreatedByName()]) && instance_of($repository->mapped_users[$this->getCreatedByName()], 'SourceUser')) {
  		$source_user = $repository->mapped_users[$this->getCreatedByName()];
  		$system_user = $source_user->system_user;
  		if(instance_of($system_user, 'User')) {
  			return $system_user->getDisplayName(true);
  		} // if
  	} // if
  	
  	return $this->getCreatedByName();
  } // getPortalAuthor
  
  /**
   * Set createdBy info
   *
   * @param User $created_by
   * @return null
   */
  function setCreatedBy($created_by) {
    $this->setCreatedById($created_by->getId());
    $this->setCreatedByName($created_by->getName());
    $this->setCreatedByEmail($created_by->getEmail());
  } // setCreatedBy
  
  /**
   * Get CreatedBy information
   *
   * @param Repository $repository
   * @return User
   */
  function getCreatedBy($repository = null) {
    if (is_null($repository)) {
      $repository = Repositories::findById($this->getParentId());
      $repository->mapped_users = SourceUsers::findByRepository($repository);
    } // if
    
    if (isset($repository->mapped_users[$this->getCreatedByName()]) && instance_of($repository->mapped_users[$this->getCreatedByName()], 'SourceUser')) {
      $source_user = $repository->mapped_users[$this->getCreatedByName()];
      if (instance_of($source_user->system_user, 'User')) {
        return $source_user->system_user;
      } // if
    } // if
    
    return parent::getCreatedBy();
  } // getCreatedBy
  
  /**
   * Return object name
   *
   * @return string
   */
  function getName() {
    return lang('Revision #:num', array('num' => $this->getRevision()));
  } // getName

  /**
   * Set diff
   *
   * @param array $value
   * @return boolean
   */
  function setDiff($value) {
    return $this->setTextField2(serialize($value));
  } // set diff

  /**
   * Get diff
   *
   * @param null
   * @return array
   */
  function getDiff() {
    return unserialize($this->getTextField2());
  } // get diff

  /**
   * Get commit revision
   *
   * @return null
   */
  function getRevision() {
    return $this->getIntegerField1();
  } // get revision

  /**
   * Set revision
   *
   * @param integer $value
   * @return null
   */
  function setRevision($value) {
    return $this->setIntegerField1($value);
  } // set revision


  /**
   * Set paths
   *
   * @param array $value
   * @return boolean
   */
  function setPaths($value) {
    return $this->setTextField1(serialize($value));
  } // set paths

  /**
   * Get commit paths
   *
   * @return array
   */
  function getPaths() {
    return unserialize($this->getTextField1());
  } // get paths

  /**
   * Get commit message
   *
   * @return string
   */
  function getMessage() {
    return $this->getBody();
  } // getMessage

  
  /**
   * Set commit message
   *
   * @param string $value
   * @return boolean
   */
  function setMessage($value) {
    return $this->setBody($value);
  } // setMessage
  
  
  /**
   * Get View URL
   *
   * @return string
   */
  function getViewUrl() {
    return assemble_url('repository_commit', array('project_id'=>$this->getProjectId(), 'repository_id'=>$this->getParentId(), 'r'=>$this->getRevision()));
  } // getViewUrl
  
  /**
   * Return portal revision view URL
   *
   * @param Portal $portal
   * @return string
   */
  function getPortalViewUrl($portal) {
  	return assemble_url('portal_repository_commit', array('portal_name' => $portal->getSlug(), 'repository_id' => $this->getParentId(), 'r' => $this->getRevision()));
  } // getPortalViewUrl
  
  
  /**
   * Get Edit URL
   * 
   * Basically, commits are history entries and are not meant to be edited, but the method is here for
   * possible compatibility issues with project object model
   *
   * @return string
   */
  function getEditUrl() {
    return '';  
  } // getEditUrl

  // ---------------------------------------------------
  //  System
  // ---------------------------------------------------

  function validate(&$errors) {
    return parent::validate($errors, true);
  }

  /**
   * Save into database
   * 
   * @return boolean
   */
  function save() {
    $save = parent::save();
    return $save;
  } // save

} // Ticket

?>