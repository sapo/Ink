<?php

  /**
   * Repository created activity log class
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class RepositoryCreatedActivityLog extends ActivityLog {
  	
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
      return get_image_url('activity_log/added.gif', SOURCE_MODULE);
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
      
      if(instance_of($object, 'Repository')) {
        $created_by = $this->getCreatedBy();
        
        $lang_params = array(
          'user_name' => $created_by->getDisplayName(true), 
          'user_url'  => $created_by->getViewUrl(),
          'url'       => $object->getViewUrl(),
          'name'      => $object->getName(),
        );
        
        if ($in_project) {
          return lang('<a href=":user_url">:user_name</a> added <a href=":url">:name</a> repository', $lang_params);
        } // if

        $project = $object->getProject();
        $lang_params['project_name'] = $project->getName(); 
        $lang_params['project_view_url'] = $project->getOverviewUrl();
        return lang('<a href=":user_url">:user_name</a> added <a href=":url">:name</a> repository in <a href=":project_view_url">:project_name</a> project', $lang_params);
      } // if
      
      return '';
    } // render
    
    // ---------------------------------------------------
    //  Portals methods
    // ---------------------------------------------------
    
    /**
     * Render portal log head details
     *
     * @param Portal $portal
     * @param ProjectObject $object
     * @param boolean $in_project
     * @return string
     */
    function renderPortalHead($portal, $object = null, $in_portal = false) {
    	if($object === null) {
    		$object = $this->getObject();
    	} // if
    	
    	$rendered = '';
    	if(instance_of($portal, 'Portal') && instance_of($object, 'Repository')) {
    		$created_by = $this->getCreatedBy();
    		
    		$rendered .= lang(':user_name added <a href=":object_url">:object_name</a> repository', array(
    			'user_name'   => $created_by->getDisplayName(true),
    			'object_url'  => $object->getPortalViewUrl($portal),
    			'object_name' => $object->getName()
    		));
    	} // if
    	
    	return $rendered;
    } // renderPortalHead
    
  }

?>