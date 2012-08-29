<?php

  /**
   * Init discussions module
   *
   * @package activeCollab.modules.discussions
   */
  
  define('DISCUSSIONS_MODULE', 'discussions');
  define('DISCUSSIONS_MODULE_PATH', APPLICATION_PATH . '/modules/discussions');
  
  set_for_autoload(array(
    'Discussion' => DISCUSSIONS_MODULE_PATH . '/models/discussions/Discussion.class.php',
    'Discussions' => DISCUSSIONS_MODULE_PATH . '/models/discussions/Discussions.class.php',
    'DiscussionPinnedActivityLog' => DISCUSSIONS_MODULE_PATH . '/models/activity_logs/DiscussionPinnedActivityLog.class.php',
    'DiscussionUnpinnedActivityLog' => DISCUSSIONS_MODULE_PATH . '/models/activity_logs/DiscussionUnpinnedActivityLog.class.php',
  ));
  
  /**
   * Return section URL
   *
   * @param Project $project
   * @return string
   */
  function discussions_module_url($project) {
    return assemble_url('project_discussions', array('project_id' => $project->getId()));
  } // discussions_module_url
  
  /**
   * Return add discussion URL
   *
   * @param Project $project
   * @param array $additional_params
   * @return string
   */
  function discussions_module_add_discussion_url($project, $additional_params = null) {
    $params = array('project_id' => $project->getId());
    
    if($additional_params !== null) {
      if(isset($additional_params['category_id'])) {
        $params['category_id'] = $additional_params['category_id'];
      } // if
      if(isset($additional_params['milestone_id'])) {
        $params['milestone_id'] = $additional_params['milestone_id'];
      } // if
    } // if
    
    return assemble_url('project_discussions_add', $params);
  } // discussions_module_add_discussion_url
  
  // ---------------------------------------------------
  //  Portals public methods
  // ---------------------------------------------------
  
  /**
   * Return portal discussions section URL
   *
   * @param Portal $portal
   * @return string
   */
  function portal_discussions_module_url($portal) {
  	return assemble_url('portal_discussions', array('portal_name' => $portal->getSlug()));
  } // portal_discussions_module_url
  
  /**
   * Return add discussion URL via public portal
   *
   * @param Portal $portal
   * @param Project $project
   * @param array $additional_params
   * @return string
   */
  function portal_discussions_module_add_discussion_url($portal, $project, $additional_params = null) {
  	$params = array('portal_name' => $portal->getSlug());
  	
  	if($additional_params !== null) {
  		$params = array_merge($params, $additional_params);
  	} // if
  	
  	return assemble_url('portal_discussions_add', $params);
  } // portal_discussions_module_add_discussion_url

?>