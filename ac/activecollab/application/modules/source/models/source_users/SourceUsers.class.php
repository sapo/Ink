<?php

  /**
   * SourceUsers class
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SourceUsers extends BaseSourceUsers {
    
    /**
     * Find users by repository
     *
     * @param Repository $repository
     * @return array
     */
    function findByRepository($repository) {
      if (!instance_of($repository, 'Repository') || $repository->isNew()) {
        return array(); // special cases when mapped users aren't needed
      } // if
      
      $source_users = SourceUsers::find(array(
        'conditions' => "repository_id = ".$repository->getId(),
        'order'         => 'repository_user asc'
      ));
      
      $source_users_array = array();
      if (is_foreachable($source_users)) {
        foreach ($source_users as $source_user) {
        	$source_user->setSystemUser();
        	$source_users_array[$source_user->getRepositoryUser()] = $source_user;
        } // foreach
      } // if

      return $source_users_array;
    } // findByRepositoryId
    
    /**
     * Get repository user
     *
     * @param string $repository_user
     * @param Repository $repository
     * @return SourceUser
     */
    function findByRepositoryUser($repository_user, $repository_id) {
      $source_user = SourceUsers::find(array(
        'conditions' => "repository_user = '$repository_user' AND repository_id = '$repository_id'",
        'one' => true
      ));
      
      if (instance_of($source_user, 'SourceUser')) {
        $source_user->setSystemUser();
      }
      return $source_user;
    } // findByRepositoryUser
  }

?>