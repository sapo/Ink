<?php

  /**
   * Init discussions module
   *
   * @package activeCollab.modules.checklists
   */
  
  define('CHECKLISTS_MODULE', 'checklists');
  define('CHECKLISTS_MODULE_PATH', APPLICATION_PATH . '/modules/checklists');
  
  set_for_autoload(array(
    'Checklist' => CHECKLISTS_MODULE_PATH . '/models/Checklist.class.php',
    'Checklists' => CHECKLISTS_MODULE_PATH . '/models/Checklists.class.php'
  ));
  
  /**
   * Return section URL
   *
   * @param Project $project
   * @return string
   */
  function checklists_module_url($project) {
    return assemble_url('project_checklists', array('project_id' => $project->getId()));
  } // checklists_module_url
  
  /**
   * Return checklist archive URL
   *
   * @param Project $project
   * @return string
   */
  function checklists_module_archive_url($project) {
    return assemble_url('project_checklists_archive', array('project_id' => $project->getId()));
  } // checklists_module_archive_url
  
  /**
   * Return add checklist ULRL
   *
   * @param Project $project
   * @param array $additional_params
   * @return string
   */
  function checklists_module_add_checklist_url($project, $additional_params = null) {
    $params = array('project_id' => $project->getId());
    if($additional_params !== null) {
      $params = array_merge($params, $additional_params);
    } // if
    return assemble_url('project_checklists_add', $params);
  } // checklists_module_add_checklist_url

?>