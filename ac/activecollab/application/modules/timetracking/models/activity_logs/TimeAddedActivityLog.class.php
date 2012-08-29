<?php

  /**
   * Time logged activity log entry
   *
   * @package activeCollab.modules.timetracking
   * @subpackage models
   */
  class TimeAddedActivityLog extends ActivityLog {
    /**
     * Action name
     *
     * @var string
     */
    var $action_name = 'Added';
    
    /**
     * Return log icon URL
     *
     * @param void
     * @return string
     */
    function getIconUrl() {
      return get_image_url('activity_log/created.gif', TIMETRACKING_MODULE);
    } // getIconUrl
    
    /**
     * Render log details
     *
     * @param TimeRecord $object
     * @param boolean $in_project
     * @return string
     */
    function renderHead($object = null, $in_project = false) {
      if($object === null) {
        $object = $this->getObject();
      } // if
      
      if(instance_of($object, 'TimeRecord')) {
        $created_by = $this->getCreatedBy();
        
        $variables = array(
          'user_name' => $created_by->getDisplayName(true), 
          'user_url' => $created_by->getViewUrl(),
          'url' => $object->getViewUrl(),
          'value' => $object->getValue(),
          'status' => $object->isBillable() ? lang('billable') : lang('non-billable'),
        );
        
        $parent = $object->getParent();
        if(instance_of($parent, 'ProjectObject')) {
          $variables['parent_name'] = $parent->getName();
          $variables['parent_type'] = $parent->getVerboseType(true);
          $variables['parent_url'] = $parent->getViewUrl();
        } else {
          $project = $object->getProject();
          
          $variables['parent_name'] = $project->getName();
          $variables['parent_type'] = lang('project');
          $variables['parent_url'] = $project->getOverviewUrl();
        } // if
        
        return $object->getValue() == 1 ? 
          lang('<a href=":user_url">:user_name</a> logged <a href=":url">1 :status hour</a> for <a href=":parent_url">:parent_name</a> :parent_type', $variables) : 
          lang('<a href=":user_url">:user_name</a> logged <a href=":url">:value :status hours</a> for <a href=":parent_url">:parent_name</a> :parent_type', $variables);
      } // if
      
      return '';
    } // render
    
  }

?>