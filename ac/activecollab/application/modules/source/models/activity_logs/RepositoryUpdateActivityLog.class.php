<?php

  /**
   * Repository update activity log handler
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class RepositoryUpdateActivityLog extends ActivityLog {
  	
    /**
     * Action name
     *
     * @var string
     */
    var $action_name = 'Updated';
    
    /**
     * Return log icon URL
     *
     * @param void
     * @return string
     */
    function getIconUrl() {
      return get_image_url('activity_log/updated.gif', SOURCE_MODULE);
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
        $lang_params = array(
          'url'       => $object->getViewUrl(),
          'user_name' => $object->getName(),
        );
        
        if ($in_project) {
          return lang('<a href=":url">:user_name</a> repository has been updated', $lang_params);
        } // if
          
        $project = $object->getProject();
        $lang_params['project_name'] = $project->getName(); 
        $lang_params['project_view_url'] = $project->getOverviewUrl();
        return lang('<a href=":url">:user_name</a> repository has been updated in <a href=":project_view_url">:project_name</a> project', $lang_params);
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
    		$rendered .= lang('<a href=":repository_url">:repository_name</a> repository has been updated', array(
    			'repository_url'  => $object->getPortalViewUrl($portal),
    			'repository_name' => $object->getName()
    		));
    	} // if
    	
    	return $rendered;
    } // renderPortalHead
    
  }

?>