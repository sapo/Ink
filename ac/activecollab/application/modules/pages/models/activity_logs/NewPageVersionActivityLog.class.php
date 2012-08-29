<?php

  /**
   * New page version activity log entry
   *
   * @package activeCollab.modules.pages
   * @subpackage models
   */
  class NewPageVersionActivityLog extends ActivityLog {
    /**
     * Action name
     *
     * @var string
     */
    var $action_name = 'New Revision';
    
    /**
     * Render log details
     *
     * @param ProjectObject $object
     * @param boolean $in_project
     * @return string
     */
    function renderHead($object = null, $in_project = false) {
      if($object === null) {
        $object = $this->getObject();
      } // if
      
      if(instance_of($object, 'ProjectObject')) {
        $created_by = $this->getCreatedBy();
        
        $lang_params = array(
          'user_name' => $created_by->getDisplayName(true), 
          'user_url' => $created_by->getViewUrl(),
          'url' => $object->getViewUrl(),
          'name' => $object->getName()
        );
        
        if ($in_project) {
          return lang('<a href=":user_url">:user_name</a> created new version of <strong><a href=":url">:name</a></strong> page', $lang_params);
        } // if

        $project = $object->getProject();
        $lang_params['project_name'] = $project->getName(); 
        $lang_params['project_view_url'] = $project->getOverviewUrl();
        return lang('<a href=":user_url">:user_name</a> created new version of <strong><a href=":url">:name</a></strong> page in <a href=":project_view_url">:project_name</a> project', $lang_params);
      } // if
      
      return '';
    } // render
    
  }

?>