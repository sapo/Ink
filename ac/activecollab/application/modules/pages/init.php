<?php

  /**
   * Init pages module
   *
   * @package activeCollab.modules.pages
   */
  
  define('PAGES_MODULE', 'pages');
  define('PAGES_MODULE_PATH', APPLICATION_PATH . '/modules/pages');
  
  set_for_autoload(array(
    'Page' => PAGES_MODULE_PATH . '/models/Page.class.php',
    'Pages' => PAGES_MODULE_PATH . '/models/Pages.class.php',
    'NewPageVersionActivityLog' => PAGES_MODULE_PATH . '/models/activity_logs/NewPageVersionActivityLog.class.php',
  ));
  use_model('page_versions', PAGES_MODULE);
  
  /**
   * Return pages section URL
   *
   * @param Project $project
   * @return string
   */
  function pages_module_url($project) {
    return assemble_url('project_pages', array('project_id' => $project->getId()));
  } // pages_module_url
  
  /**
   * Return add page ULRL
   *
   * @param Project $project
   * @param array $additional_params
   * @return string
   */
  function pages_module_add_page_url($project, $additional_params = null, $parent = null) {
    $params = array('project_id' => $project->getId());
    
    if($additional_params !== null) {
      $parent = array_var($additional_params, 'parent');
      if(instance_of($parent, 'Page') || instance_of($parent, 'Category')) {
        $params['parent_id'] = $parent->getId();
      } // if
      if(isset($additional_params['milestone_id'])) {
        $params['milestone_id'] = $additional_params['milestone_id'];
      } // if
    } // if
    
    return assemble_url('project_pages_add', $params);
  } // pages_module_add_page_url

?>