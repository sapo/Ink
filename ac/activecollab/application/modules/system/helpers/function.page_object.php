<?php

  /**
   * page_object helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Set page properties with following object
   * 
   * Parameters:
   * 
   * - object - Application object instance
   *
   * @param array $params
   * @param Smarty $smarty
   * @return null
   */
  function smarty_function_page_object($params, &$smarty) {
    static $private_roles = false;
    
    $object = array_var($params, 'object');
    if(!instance_of($object, 'ApplicationObject')) {
      return new InvalidParamError('object', $object, '$object is expected to be an instance of ApplicationObject class', true);
    } // if
    
    require_once SMARTY_DIR . '/plugins/modifier.datetime.php';
    
    $wireframe =& Wireframe::instance();
    $logged_user =& get_logged_user();
    
    $construction =& PageConstruction::instance();
    if($construction->page_title == '') {
      $construction->setPageTitle($object->getName());
    } // if
    
    if(instance_of($object, 'ProjectObject') && $wireframe->details == '') {
      $in = $object->getParent();
      $created_on = $object->getCreatedOn();
      $created_by = $object->getCreatedBy();
      
      if(instance_of($created_by, 'User') && instance_of($in, 'ApplicationObject') && instance_of($created_on, 'DateValue')) {
        $wireframe->details = lang('By <a href=":by_url">:by_name</a> in <a href=":in_url">:in_name</a> on <span>:on</span>', array(
          'by_url' => $created_by->getViewUrl(),
          'by_name' => $created_by->getDisplayName(),
          'in_url' => $in->getViewUrl(),
          'in_name' => $in->getName(),
          'on' => smarty_modifier_datetime($created_on),
        ));
      } elseif(instance_of($created_by, 'User') && instance_of($created_on, 'DateValue')) {
        $wireframe->details = lang('By <a href=":by_url">:by_name</a> on <span>:on</span>', array(
          'by_url' => $created_by->getViewUrl(),
          'by_name' => $created_by->getDisplayName(),
          'on' => smarty_modifier_datetime($created_on),
        ));
      } elseif(instance_of($created_by, 'User')) {
        $wireframe->details = lang('By <a href=":by_url">:by_name</a>', array(
          'by_url' => $created_by->getViewUrl(),
          'by_name' => $created_by->getDisplayName(),
        ));
      } elseif(instance_of($created_by, 'AnonymousUser') && instance_of($created_on, 'DateValue')) {
        $wireframe->details = lang('By <a href=":by_url">:by_name</a> on <span>:on</span>', array(
          'by_url' => 'mailto:' . $created_by->getEmail(),
          'by_name' => $created_by->getName(),
          'on' => smarty_modifier_datetime($created_on),
        ));
      } elseif(instance_of($created_by, 'AnonymousUser')) {
        $wireframe->details = lang('By <a href=":by_url">:by_name</a>', array(
          'by_url' => 'mailto:' . $created_by->getEmail(),
          'by_name' => $created_by->getName(),
        ));
      } // if
    } // if
    
    $smarty->assign('page_object', $object);
    
    // Need to do a case sensitive + case insensitive search to have PHP4 covered
    $class_methods = get_class_methods($object);
    
    if(in_array('getOptions', $class_methods) || in_array('getoptions', $class_methods)) {
      $options = $object->getOptions($logged_user);
      if(instance_of($options, 'NamedList') && $options->count()) {
        $wireframe->addPageAction(lang('Options'), '#', $options->data, array('id' => 'project_object_options'), 1000);
      } // if
      
      if(instance_of($object, 'ProjectObject')) {
        if($object->getState() > STATE_DELETED) {
          if($object->getVisibility() <= VISIBILITY_PRIVATE) {
            if($private_roles === false) {
              $private_roles = who_can_see_private_objects(true, lang(' or '));
            } // if
            
            $project = $object->getProject();
            $project_name  = instance_of($project, 'Project') ? $project->getName() : lang('Unknown Project');
            
            $wireframe->addPageMessage(lang('<b>Private</b> - only members with: :roles roles who are involved with ":project" project can see this :type.', array('type' => $object->getVerboseType(true), 'roles' => $private_roles, 'project' => $project_name)), PAGE_MESSAGE_PRIVATE);
          } // if
        } else {
          $wireframe->addPageMessage(lang('<b>Trashed</b> - this :type is located in trash.', array('type' => $object->getVerboseType(true))), PAGE_MESSAGE_TRASHED);
        } // if
      } // if
    } // if
    
    return '';
  } // smarty_function_page_object

?>