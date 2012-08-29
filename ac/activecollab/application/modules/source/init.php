<?php

  /**
   * Source module initialization file
   *
   * @package activeCollab.modules.source
   */

  // module basics
  define('SOURCE_MODULE', 'source');
  define('SOURCE_MODULE_PATH', APPLICATION_PATH . '/modules/source');
  
  
  // load modules
  require_once SOURCE_MODULE_PATH.'/models/commits/Commit.class.php';
  require_once SOURCE_MODULE_PATH.'/models/commits/Commits.class.php';
  require_once SOURCE_MODULE_PATH.'/models/repositories/Repository.class.php';
  require_once SOURCE_MODULE_PATH.'/models/repositories/Repositories.class.php';
  use_model('commit_project_objects', SOURCE_MODULE);
  use_model('source_users', SOURCE_MODULE);
  
  set_for_autoload(array(
    'RepositoryCreatedActivityLog' => SOURCE_MODULE_PATH . '/models/activity_logs/RepositoryCreatedActivityLog.class.php',
    'RepositoryUpdateActivityLog' => SOURCE_MODULE_PATH . '/models/activity_logs/RepositoryUpdateActivityLog.class.php',
  ));
  
  define('REPOSITORY_UPDATE_FREQUENTLY', 1);
  define('REPOSITORY_UPDATE_HOURLY', 2);
  define('REPOSITORY_UPDATE_DAILY', 3);
  // define('REPOSITORY_UPDATE_HOOK', 4);
  
  /**
   * List of update types
   *
   * @param null
   * @return array
   */
  function source_module_update_types() {
    return array(
      REPOSITORY_UPDATE_FREQUENTLY  => lang('Frequently'),
      REPOSITORY_UPDATE_HOURLY      => lang('Hourly'),
      REPOSITORY_UPDATE_DAILY       => lang('Daily'),
      // REPOSITORY_UPDATE_HOOK        => lang('On Commit Hook'),
      );
  } // source module update types
  
  
  /**
   * Supported source version control systems
   *
   * @param null
   * @return array
   */
  function source_module_types() {
    return array(
      '1' => 'Subversion',
    );
  } // source module types
  
  
  /**
   * Get the URL of source module
   *
   * @param object $project
   * @return string
   */
  function source_module_url($project) {
    return assemble_url('project_repositories', array('project_id' => $project->getId()));
  } // source module URL
  
  
  /**
   * Get the URL to add a repository
   *
   * @param object $project
   * @return string
   */
  function source_module_add_repository_url($project) {
    return assemble_url('repository_add',array('project_id'=>$project->getId()));
  } // add a repository URL
  
  define('SOURCE_MODULE_STATE_ADDED', 'A');
  define('SOURCE_MODULE_STATE_DELETED', 'D');
  define('SOURCE_MODULE_STATE_IGNORED', 'I');
  define('SOURCE_MODULE_STATE_UPDATED', 'U');
  define('SOURCE_MODULE_STATE_MODIFIED', 'M');
  define('SOURCE_MODULE_STATE_MERGED', 'G');
  define('SOURCE_MODULE_STATE_CONFLICTED', 'C');
  define('SOURCE_MODULE_STATE_NOT_VERSIONED', '?');
  define('SOURCE_MODULE_STATE_MISSING', '!');
  define('SOURCE_MODULE_STATE_BE_MOVED', 'A+');
  
  /**
   * Return descriptive SVN state
   *
   * @param string $code
   * @return string
   */
  function source_module_get_state_string($code) {
    
    $status_codes = array(
      SOURCE_MODULE_STATE_ADDED   => lang('Added'),
      SOURCE_MODULE_STATE_DELETED   => lang('Deleted'),
      SOURCE_MODULE_STATE_IGNORED   => lang('Ignored'),
      SOURCE_MODULE_STATE_MODIFIED   => lang('Modified'),
      SOURCE_MODULE_STATE_UPDATED   => lang('Updated'),
      SOURCE_MODULE_STATE_MERGED   => lang('Merged into working copy'),
      SOURCE_MODULE_STATE_CONFLICTED   => lang('Conflict'),
      SOURCE_MODULE_STATE_NOT_VERSIONED   => lang('Not under version control'),
      SOURCE_MODULE_STATE_MISSING   => lang('Missing or incomplete'),
      SOURCE_MODULE_STATE_BE_MOVED  => lang('Will be moved after commit'),
    );
    
    $keys = array_keys($status_codes);
    
    if (in_array($code, $keys)) {
      return $status_codes[$code];
    } else {
      return lang('Unknown');
    } // if
  } // get_source_module_state_string
  
  // ---------------------------------------------------
  //  Portals methods
  // ---------------------------------------------------
  
  /**
   * Get the URL of source module
   *
   * @param Portal $portal
   * @return string
   */
  function portal_source_module_url($portal) {
  	return assemble_url('portal_repositories', array('portal_name' => $portal->getSlug()));
  } // portal_source_module_url

?>