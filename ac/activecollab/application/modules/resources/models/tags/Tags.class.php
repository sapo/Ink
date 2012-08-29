<?php

  /**
  * Tags manager
  * 
  * Container class for all tag related functions
  *
  * @author Ilija Studen <ilija.studen@gmail.com>
  */
  class Tags {
    
    /**
    * Return tags section URL
    *
    * @param Project $project
    * @return string
    */
    function getTagsUrl($project) {
      return assemble_url('project_tags', array(
        'project_id' => $project->getId(),
      ));
    } // getTagsUrl
  
    /**
    * Return tag URL
    *
    * @param string $tag
    * @param Project $project
    * @return string
    */
    function getTagUrl($tag, $project, $page = null) {
      $params = array(
        'tag' => urlencode($tag),
        'project_id' => $project->getId(),
      );
      
      if($page !== null) {
        $params['page'] = $page;
      } // if
      
      return assemble_url('project_tag', $params);
    } // getTagUrl
    
    /**
    * Return tags index for a given project
    * 
    * Index is an associative array of tags where key is the tag and value is 
    * associative array of options:
    * 
    * - objects - IDs of all objects that have tags
    * - private_object - IDs of objects marked as private
    * - normal_objects - IDs of objects marked as normal
    * - public_objects - IDs of objects marked as public
    *
    * @param Project $project
    * @return array
    */
    function buildIndex($project) {
      $result = array();
      
      $rows = db_execute_all('SELECT id, visibility, tags FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND tags != ?', $project->getId(), '');
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $tags = Tags::toTags($row['tags']);
          if(is_foreachable($tags)) {
            foreach($tags as $tag) {
              if(!isset($result[$tag])) {
                $result[$tag] = array(
                  'objects' => array(),
                  'private_object' => array(),
                  'normal_objects' => array(),
                  'public_objects' => array(),
                ); // array
              } // if
              
              $result[$tag]['objects'][] = $row['id'];
              switch($row['visibility']) {
                case VISIBILITY_PRIVATE:
                  $result[$tag]['private_object'][] = $row['id'];
                  break;
                case VISIBILITY_PRIVATE:
                  $result[$tag]['normal_objects'][] = $row['id'];
                  break;
                case VISIBILITY_PUBLIC:
                  $result[$tag]['public_objects'][] = $row['id'];
                  break;
              } // switch
            } // foreach
          } // if
        } // foreach
      } // if
      
      return $result;
    } // buildIndex
    
    /**
    * Convert array of tags to string
    *
    * @param array $tags
    * @return string
    */
    function toString($tags) {
      if(is_foreachable($tags)) {
        foreach($tags as $k => $v) {
          $tags[$k] = strtolower(trim($v));
        } // if
        return implode(', ', $tags);
      } elseif(is_string($tags)) {
        return $tags;
      } else {
        return '';
      } // if
    } // toString
    
    /**
    * Convert string to tags
    *
    * @param string $string
    * @return array
    */
    function toTags($string) {
      $string = trim($string);
      if($string) {
        $tags = explode(',', $string);
        if(is_foreachable($tags)) {
          foreach($tags as $k => $v) {
            $tags[$k] = strtolower_utf(trim($v));
          } // foreach
          return $tags;
        } // if
      } // if
      return array();
    } // toTags
    
    // ---------------------------------------------------
    //  Portal methods
    // ---------------------------------------------------
    
    /**
     * Returns portal tags section URL
     *
     * @param Portal $portal
     * @return string
     */
    function getPortalTagsUrl($portal) {
    	return assemble_url('portal_tags', array('portal_name' => $portal->getSlug()));
    } // getPortalTagsUrl
    
    /**
     * Returns portal tag URL
     *
     * @param string $tag
     * @param Portal $portal
     * @param integer $page
     * @return string
     */
    function getPortalTagUrl($tag, $portal, $page = null) {
    	$params = array(
    		'portal_name' => $portal->getSlug(),
    		'tag'         => urlencode($tag)
    	);
    	
    	if($page !== null) {
    		$params['page'] = $page;
    	} // if
    	
    	return assemble_url('portal_tag', $params);
    } // getPortalTagUrl
    
    /**
     * Return portal tags index for a given portal project ID
     * 
     * Index is an associative array of tags where key is the tag and value is
     * associative array of options:
     * 
     * - objects - IDs of all objects that have tags
     * - normal objects - IDs of objects marked as normal
     *
     * @param integer $project_id
     * @return array
     */
    function buildPortalIndex($project_id) {
    	$result = array();
    	
    	$rows = db_execute_all('SELECT id, visibility, tags FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND tags != ? AND visibility >= ?', $project_id, '', VISIBILITY_NORMAL);
    	if(is_foreachable($rows)) {
    		foreach($rows as $row) {
    			$tags = Tags::toTags($row['tags']);
    			if(is_foreachable($tags)) {
    				foreach($tags as $tag) {
    					if(!isset($result[$tag])) {
    						$result[$tag] = array(
    							'objects'        => array(),
    							'normal_objects' => array(),
    							'public_objects' => array()
    						);
    					} // if
    					
    					$result[$tag]['objects'][] = $row['id'];
    					switch($row['visibility']) {
    						case VISIBILITY_NORMAL:
    							$result[$tag]['normal_objects'][] = $row['id'];
    							break;
    						case VISIBILITY_PUBLIC:
    							$result[$tag]['public_objects'][] = $row['id'];
    							break;
    					} // switch
    				} // foreach
    			} // if
    		} // foreach
    	} // if
    	
    	return $result;
    } // buildPortalIndex
  
  }

?>