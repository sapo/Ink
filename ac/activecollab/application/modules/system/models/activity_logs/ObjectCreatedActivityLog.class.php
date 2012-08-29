<?php

  /**
   * Object created activity log entry
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ObjectCreatedActivityLog extends ActivityLog {
  	
    /**
     * Action name
     *
     * @var string
     */
    var $action_name = 'Created';
    
    /**
     * Indicates whether this activity log renders body information or not
     *
     * @var boolean
     */
    var $has_body = true;
    
    /**
     * Indicates whether this activity log renders footer information or not
     *
     * @var boolean
     */
    var $has_footer = true;
    
    /**
     * Return log icon URL
     *
     * @param void
     * @return string
     */
    function getIconUrl() {
      return get_image_url('activity_log/created.gif');
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
          'type'      => $object->getVerboseType(true),
        );
        
        if ($in_project) {
          return lang('<a href=":user_url">:user_name</a> created new <strong><a href=":url">:name</a></strong> :type', $lang_params);
        } // if
                  
        $project = $object->getProject();
        $lang_params['project_name'] = $project->getName(); 
        $lang_params['project_view_url'] = $project->getOverviewUrl();
        return lang('<a href=":user_url">:user_name</a> created new <strong><a href=":url">:name</a></strong> :type in <a href=":project_view_url">:project_name</a> project', $lang_params);
      } // if
      
      return '';
    } // render
    
    /**
     * Render log details
     * 
     * @param ProjectObject $object
     * @param boolean $in_project
     * @return string
     */
    function renderBody($object = null, $in_project = false) {
      require_once SMARTY_PATH . '/plugins/modifier.html_excerpt.php';
      if($object === null) {
        $object = $this->getObject();
      } // if
      
      $result = '';
      if (instance_of($object, 'ProjectObject')) {
        $excerpt_body = trim(str_excerpt(smarty_modifier_html_excerpt($object->getFormattedBody(true, true)), 200));
        if ($excerpt_body) {
          $result.= '<div class="new_object_body"><strong>"</strong>' . $excerpt_body . '<strong>"</strong></div>';
        } // if
      } // if
      return $result;
    } // renderBody
    
    /**
     * Render Log Details
     *
     * @param ProjectObject $object
     * @param boolean $in_project
     */
    function renderFooter($object = null, $in_project = false) {
      $links = array();
      
      if (instance_of($object,  'ProjectObject') && $object->getCommentsCount() > 0) {
        $label = lang('View Comments (:count)', array('count' => $object->getCommentsCount()));
        $links[] = '<a href="'.$object->getViewUrl().'#comments">'.$label.'</a>';
      } // if
      
      return implode(' &middot; ', $links);
    } // if
    
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
    		
    		$rendered .= lang(':user_name created new <strong><a href=":object_url">:object_name</a></strong> :object_type', array(
    			'user_name'   => $created_by->getDisplayName(true),
    			'object_url'  => $object->getPortalViewUrl($portal),
    			'object_name' => $object->getName(),
    			'object_type' => $object->getVerboseType(true)
    		));
    	} // if
    	
    	return $rendered;
    } // renderPortalHead
    
    /**
     * Render portal log footer details
     *
     * @param Portal $portal
     * @param ProjectObject $object
     * @param boolean $in_project
     * @return string
     */
    function renderPortalFooter($portal, $object = null, $in_project = false) {
    	if($object === null) {
    		$object = $this->getObject();
    	} // if
    	
    	$links = array();
    	if(instance_of($portal, 'Portal') && instance_of($object, 'ProjectObject') && $object->getCommentsCount() > 0) {
    		$label = lang('View Comments (:count)', array('count' => $object->getCommentsCount()));
    		$links[] = '<a href="' . $object->getPortalViewUrl($portal) . '#comments">' . $label . '</a>';
    	} // if
    	
    	return implode(' &middot; ', $links);
    } // renderPortalFooter
    
  }

?>