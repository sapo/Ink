<?php

  /**
   * Init discussions module
   *
   * @package activeCollab.modules.milestones
   */
  
  define('MILESTONES_MODULE', 'milestones');
  define('MILESTONES_MODULE_PATH', APPLICATION_PATH . '/modules/milestones');
  
  set_for_autoload(array(
    'Milestone' => MILESTONES_MODULE_PATH . '/models/Milestone.class.php',
    'Milestones' => MILESTONES_MODULE_PATH . '/models/Milestones.class.php'
  ));
  
  /**
   * Return section URL
   *
   * @param Project $project
   * @return string
   */
  function milestones_module_url($project) {
    return assemble_url('project_milestones', array('project_id' => $project->getId()));
  } // milestones_module_url
  
  /**
   * Return add milestone ULRL
   *
   * @param Project $project
   * @return string
   */
  function milestones_module_add_url($project) {
    return assemble_url('project_milestones_add', array('project_id' => $project->getId()));
  } // milestones_module_add_url
  
  // ---------------------------------------------------
  //  Portals public methods
  // ---------------------------------------------------
  
  /**
	 * Return portal milestones section URL
	 *
	 * @param Portal $portal
	 * @return string
	 */
	function portal_milestones_module_url($portal) {
		return assemble_url('portal_milestones', array('portal_name' => $portal->getSlug()));
	} // portal_milestones_url

?>