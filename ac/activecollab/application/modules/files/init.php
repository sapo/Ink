<?php

  /**
   * Init files module
   *
   * @package activeCollab.modules.files
   */
  
  define('FILES_MODULE', 'files');
  define('FILES_MODULE_PATH', APPLICATION_PATH . '/modules/files');
  
  set_for_autoload(array(
    'File' => FILES_MODULE_PATH . '/models/File.class.php',
    'Files' => FILES_MODULE_PATH . '/models/Files.class.php',
    'NewFileActivityLog' => FILES_MODULE_PATH . '/models/activity_logs/NewFileActivityLog.class.php',
    'NewFileVersionActivityLog' => FILES_MODULE_PATH . '/models/activity_logs/NewFileVersionActivityLog.class.php',
  ));
  
  /**
   * Return section URL
   *
   * @param Project $project
   * @param array $additional_params
   * @return string
   */
  function files_module_url($project, $additional_params = null) {
    $params = array('project_id' => $project->getId());
    
    if($additional_params) {
      if(isset($additional_params['page'])) {
        $params['page'] = $additional_params['page'];
      } // if
      
      if(isset($additional_params['category_id'])) {
        $params['category_id'] = $additional_params['category_id'];
      } // if
      
      if (isset($additional_params['show_attachments'])) {
        $params['show_attachments'] = $additional_params['show_attachments'];
      } // if
    } // if
    return assemble_url('project_files', $params);
  } // files_module_url
  
  /**
   * Return upload files URL
   *
   * @param Project $project
   * @param Category $category
   * @return string
   */
  function files_module_upload_url($project, $additional_params = null) {
  	$params = array('project_id' => $project->getId());
  	if(is_array($additional_params)) {
  	  $params = array_merge($params, $additional_params);
  	} // if
  	
  	return assemble_url('project_files_upload', $params);
  } // files_module_upload_url

?>