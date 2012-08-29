<?php

  /**
   * StatusUpdate class
   * 
   * @package activeCollab.modules.status
   * @subpackage models
   */
  class StatusUpdate extends BaseStatusUpdate {
    
    /**
     * Describe this object
     *
     * @param User $user
     * @param array $additional
     * @return array
     */
    function describe($user, $additional = null) {
      $result = array(
        'message' => $this->getMessage(),
        'created_on' => $this->getCreatedOn(),
      );
      
      $created_by = $this->getCreatedBy();
      if(instance_of($created_by, 'User')) {
        $result['created_by'] = array(
          'id'   => $created_by->getId(),
          'name' => $created_by->getDisplayName(),
        );
      } // if
      
      return $result;
    } // describe
  
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if(!$this->validatePresenceOf('message', 3)) {
        $errors->addError(lang('Status message is required'), 'message');
      } // if
    } // validate
    
    /**
     * View status update url
     * 
     * @param null
     * @return string
     */
    function getViewUrl() {
      return assemble_url('status_update', array('status_update_id' => $this->getId()));
    } // getViewUrl
    
    /**
     * returns real status update url
     *
     * @param integer $per_page
     * @return string
     */
    function getRealViewUrl($per_page=15) {      
      return assemble_url('status_updates', array(
        'page' => ceil(StatusUpdates::findStatusUpdateNumForUser($this, get_logged_user()) / $per_page)
      )).'#status_update_'.$this->getId();
    } // getRealViewUrl();

  }

?>