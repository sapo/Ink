<?php

/**
 * Repositories
 *
 */
class Repositories extends ProjectObjects {
  
  /**
   * Get repositories by project id and add last commit info
   *
   * @param int $project_id
   * @return array of objects
   */
  function findByProjectId($project_id) {
    $repositories = ProjectObjects::find(array(
      'conditions'  => "project_id = $project_id AND `type` = 'Repository' AND state >='".STATE_VISIBLE."'",
      'order' => 'id asc'
    ));
    
    if (is_foreachable($repositories)) {
      foreach ($repositories as $repository) {
      	$repository->last_commit = $repository->getLastCommit();
      }
    }

    return $repositories;
  } // find repositories by project id
  
  
  /**
   * Find all repositories that match specific update type
   *
   * @param int $update_type
   * @return array
   */
  function findByUpdateType($update_type) {
    return ProjectObjects::find(array(
      'conditions'  => "`type` = 'Repository' AND integer_field_2 = '$update_type' AND state != '".STATE_DELETED."'"
    ));
  } // find repositories by update type
  
  // ---------------------------------------------------
  //  Portal methods
  // ---------------------------------------------------
  
  /**
   * Return repository which was first added and last commit info
   *
   * @param Project $project
   * @return array
   */
  function findByPortalProject($project) {
  	$repository = ProjectObjects::find(array(
  		'conditions' => array('project_id = ? AND type = ? AND state >= ?', $project->getId(), 'Repository', STATE_VISIBLE),
  		'order'      => 'created_on ASC',
  		'one'        => true
  	));
  	
  	if(instance_of($repository, 'Repository')) {
  		$repository->last_commit = $repository->getLastCommit();
  	} // if
  	
  	return $repository;
  } // findByPortalProject
  
}

?>