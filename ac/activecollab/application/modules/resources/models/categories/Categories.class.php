<?php

  /**
   * Categories manager class
   * 
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class Categories extends ProjectObjects {
    
    /**
     * Find all categories in a given project
     *
     * @param Project $project
     * @return array
     */
    function findByProject($project) {
      return ProjectObjects::find(array(
        'conditions' => array('type = ? AND project_id = ? AND state >= ?', 'Category', $project->getId(), STATE_VISIBLE),
        'order' => 'name',
      ));
    } // findByProject
    
    /**
     * Return categories for module section (ex. attachments of resources module)
     *
     * @param Project $project
     * @param string $module
     * @param string $controller
     * @return array
     */
    function findByModuleSection($project, $module, $controller) {
      return ProjectObjects::find(array(
        'conditions' => array('type = ? AND project_id = ? AND module = ? AND state >= ? AND varchar_field_1 = ?', 'Category', $project->getId(), $module, STATE_VISIBLE, $controller),
        'order' => 'name',
      ));
    } // findByModuleSection
    
  }

?>