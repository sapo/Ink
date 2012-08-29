<?php

  /**
   * New task log entry
   *
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class NewTaskActivityLog extends ActivityLog {
  	
    /**
     * Action name
     *
     * @var string
     */
    var $action_name = 'Created';
    
    /**
     * Return log icon URL
     *
     * @param void
     * @return string
     */
    function getIconUrl() {
      return get_image_url('activity_log/task.gif', RESOURCES_MODULE);
    } // getIconUrl
    
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
          'user_url'  => $created_by->getViewUrl(),
          'url'       => $object->getViewUrl(),
          'name'      => $object->getName(),
        );
        
        if($in_project) {
          $parent = $object->getParent();
          
          if(instance_of($parent, 'ProjectObject')) {
            $lang_params['parent_name'] = $parent->getName();
            $lang_params['parent_url'] = $parent->getViewUrl();
            $lang_params['parent_type'] = $parent->getVerboseType(true);
          } // if
          
          return lang('<a href=":user_url">:user_name</a> added a new task <a href=":url">:name</a> to <a href=":parent_url">:parent_name</a> :parent_type', $lang_params);
        } else {
          $project = $object->getProject();
          
          $lang_params['project_name'] = $project->getName(); 
          $lang_params['project_view_url'] = $project->getOverviewUrl();
          
          return lang('<a href=":user_url">:user_name</a> created a new task: <a href=":url">:name</a> in <a href=":project_view_url">:project_name</a> project', $lang_params);
        } // if
      } // if
      
      return '';
    } // render
    
    // ---------------------------------------------------
    //  Portal methods
    // ---------------------------------------------------
    
    /**
     * Render portal log head details
     *
     * @param Portal $portal
     * @param ProjectObject $object
     * @param boolean $in_project
     * @return string
     */
    function renderPortalHead($portal, $object = null, $in_project = false) {
    	if($object === null) {
    		$object = $this->getObject();
    	} // if
    	
    	$rendered = '';
    	if(instance_of($portal, 'Portal') && instance_of($object, 'ProjectObject')) {
    		$created_by = $this->getCreatedBy();
    		
    		$rendered .= lang(':user_name created a new task: <a href=":object_url">:object_name</a>', array(
    			'user_name'   => $created_by->getDisplayName(true),
    			'object_url'  => $object->getPortalViewUrl($portal),
    			'object_name' => $object->getName()
    		));
    	} // if
    	
    	return $rendered;
    } // renderPortalHead
    
  }

?>