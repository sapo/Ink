<?php

  /**
   * SourceUser class
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SourceUser extends BaseSourceUser {
    
    /**
     * System user object
     *
     * @var User
     */
    var $system_user = null;
    
    /**
     * Set user object from the system
     *
     * @param void
     * @return null
     */
    function setSystemUser() {
      if (is_null($this->system_user)) {
        $this->system_user = Users::findById($this->getUserId());
      } // if
    } // getSystemUser
    
    /**
     * Get delete URL for repository user
     *
     * @param Project $project
     * @return string
     */
    function getDeleteUrl($project) {
      return assemble_url('repository_user_delete', array('repository_id' => $this->getRepositoryId(), 'project_id' => $project->getId()));
    } // getDeleteUrl
    
  } // SourceUser

?>