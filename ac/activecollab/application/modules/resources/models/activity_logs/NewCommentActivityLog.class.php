<?php

  /**
   * New comment activity log entry
   *
   * @package activeCollab.modules.resources
   * @subpackage models
   */
  class NewCommentActivityLog extends ActivityLog {
  	
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
      return get_image_url('activity_log/comment.gif', RESOURCES_MODULE);
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
        $parent = $object->getParent();
        
        if(instance_of($parent, 'ProjectObject')) {
          $lang_params = array(
            'user_name'   => $created_by->getDisplayName(true), 
            'user_url'    => $created_by->getViewUrl(),
            'parent_url'  => $parent->getViewUrl(), 
            'parent_name' => $parent->getName(), 
            'parent_type' => $parent->getVerboseType(true), 
          );
          
          if ($in_project) {
            return lang('<a href=":user_url">:user_name</a> commented <a href=":parent_url">:parent_name</a> :parent_type', $lang_params);
          } // if

          $project = $object->getProject();
          $lang_params['project_name'] = $project->getName(); 
          $lang_params['project_view_url'] = $project->getOverviewUrl();
          return lang('<a href=":user_url">:user_name</a> commented <a href=":parent_url">:parent_name</a> :parent_type in <a href=":project_view_url">:project_name</a> project', $lang_params);
        } else {
          return lang('<a href=":user_url">:user_name</a> posted a new comment:', array(
            'user_name' => $created_by->getDisplayName(true), 
            'user_url'  => $created_by->getViewUrl(),
            'url'       => $object->getViewUrl(),
          ));
        } // if
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
     
      $excerpt_body = trim(str_excerpt(smarty_modifier_html_excerpt($object->getFormattedBody(true, true)), 200));
      if ($excerpt_body) {
        $result = '<div class="comment_body">' . $excerpt_body . '</div>';
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
      if($object === null) {
        $object = $this->getObject();
      } // if
      
      // permalink
      $links[] = '<a href="' . $object->getViewUrl() . '">' . lang('View Comment') . '</a>';
      
      // link to parent comments
      $parent = $object->getParent();
      if (instance_of($parent, 'ProjectObject')) {
        $label = lang('All Comments (:count)', array('count' => $parent->getCommentsCount()));
        $links[] = '<a href="'.$parent->getViewUrl().'#comments">'.$label.'</a>';
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
    		
    		$parent = $object->getParent();
    		if(instance_of($parent, 'ProjectObject')) {
    			$rendered .= lang(':user_name commented <a href=":parent_url">:parent_name</a> :parent_type', array(
    				'user_name'   => $created_by->getDisplayName(true),
    				'parent_url'  => $parent->getPortalViewUrl($portal),
    				'parent_name' => $parent->getName(),
    				'parent_type' => $parent->getVerboseType(true)
    			));
    		} else {
    			$rendered .= lang(':user_name posted a new comment:', array(
    				'user_name' => $created_by->getDisplayName(true)
    			));
    		} // if
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
    	// permalink
    	$links[] =  '<a href="' . $object->getPortalViewUrl($portal) . '">' . lang('View Comment') . '</a>';
    	
    	// link to parent comments
    	$parent = $object->getParent();
    	if(instance_of($parent, 'ProjectObject')) {
    		$label = lang('All Comments (:count)', array('count' => $parent->getCommentsCount()));
    		$links[] = '<a href="' . $parent->getPortalViewUrl($portal) . '#comments">' . $label . '</a>';
    	} // if
    	return implode(' &middot; ', $links);
    } // renderPortalFooter
    
  }

?>