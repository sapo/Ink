<?php
  /**
   * Mobile Access module initialization file
   * 
   * @package activeCollab.modules.mobile_access
   */
  
  define('MOBILE_ACCESS_MODULE', 'mobile_access');
  define('MOBILE_ACCESS_MODULE_PATH', APPLICATION_PATH . '/modules/mobile_access');
  
  
  /**
   * Returns view url for object provided via $object parameter
   *
   * @param ProjectObject $object
   * @return string
   */
  function mobile_access_module_get_view_url(&$object) {
    if (!$object) {
      return false;
    } else if (instance_of($object,'ProjectObject')) {
      return assemble_url('mobile_access_view_'.strtolower($object->getType()), array("project_id" => $object->getProjectId(), "object_id" => $object->getId()));
    } else if (instance_of($object, 'Project')) {
      return assemble_url('mobile_access_view_project', array("project_id" => $object->getId()));
    } else if (instance_of($object, 'AnonymousUser')) {
      return 'mailto:'.$object->getEmail();
    } else if (instance_of($object, 'PageVersion')) {
      $page = $object->getPage();
      return assemble_url('mobile_access_view_page_version', array("object_id" => $object->getPageId(), 'version' => $object->getVersion(), 'project_id' => $page->getProjectId()));
    } else {
      return assemble_url('mobile_access_view_'.strtolower(get_class($object)), array("object_id" => $object->getId()));
    }// if
  } // mobile_access_module_get_view_url

  /**
   * Returns add comment url for object provided via $object parameter
   *
   * @param ProjectObject $object
   * @return string
   */
  function mobile_access_module_get_add_comment_url(&$object) {
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    return assemble_url('mobile_access_add_comment', array('project_id' => $object->getProjectId(), 'parent_id' => $object->getId()));
  } // mobile_access_module_get_add_comment_url
  
  /**
   * Returns url that toggles object completed state
   *
   * @param ProjectObject $object
   * @return string
   */
  function mobile_access_module_get_task_toggle_url(&$object) {
    if(!instance_of($object, 'ProjectObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ProjectObject class', true);
    } // if
    
    return assemble_url('mobile_access_toggle_object_completed_status', array("project_id" => $object->getProjectId(), "object_id" => $object->getId()));
  } // mobile_access_module_get_task_toggle_url
  
  /**
   * Returns supported device in dependance of $device
   *
   */
  function mobile_access_module_get_compatible_device($device) {
    $compatible_devices = array(
      USER_AGENT_IPHONE => array(USER_AGENT_IPHONE, USER_AGENT_IPOD_TOUCH, USER_AGENT_ANDROID),
      USER_AGENT_SYMBIAN => array(USER_AGENT_SYMBIAN, USER_AGENT_OPERA_MINI, USER_AGENT_BLACKBERRY, USER_AGENT_MOBILE_IE, USER_AGENT_OPERA_MOBILE),
    );
    
    foreach ($compatible_devices as $compatible_device => $device_list) {
    	if (in_array($device,$device_list)) return $compatible_device;
    } // foreach
    
    return USER_AGENT_DEFAULT_MOBILE;
  } // mobile_access_module_get_compatible_device
  
?>