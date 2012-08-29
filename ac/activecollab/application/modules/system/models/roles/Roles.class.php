<?php

  /**
   * Roles manager class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Roles extends BaseRoles {
  
    /**
     * Return all roles defined in system
     *
     * @param void
     * @return array
     */
    function findAll() {
      return Roles::find(array(
        'order' => 'name'
      ));
    } // findAll
    
    /**
     * Return system roles
     *
     * @param void
     * @return array
     */
    function findSystemRoles() {
      return Roles::find(array(
        'conditions' => array('type = ?', ROLE_TYPE_SYSTEM),
        'order' => 'name'
      ));
    } // findSystem
    
    /**
     * Return project roles
     *
     * @param void
     * @return array
     */
    function findProjectRoles() {
      return Roles::find(array(
        'conditions' => array('type = ?', ROLE_TYPE_PROJECT),
        'order' => 'name'
      ));
    } // findProject
    
    /**
     * Find indexed roles by role IDs
     *
     * @param void
     * @return array
     */
    function findIndexedByIds($ids) {
      $result = array();
      if(is_foreachable($ids)) {
        $roles = Roles::find(array(
          'conditions' => array('id IN (?)', $ids),
        ));
        if(is_foreachable($roles)) {
          foreach($roles as $role) {
            $result[$role->getId()] = $role;
          } // foreach
        } // if
      } // if
      return $result;
    } // findIndexedByIds
  
  }

?>