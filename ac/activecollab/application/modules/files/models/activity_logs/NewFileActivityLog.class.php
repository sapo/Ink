<?php

  /**
   * New file activity log entry
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class NewFileActivityLog extends ActivityLog {
    
    /**
     * Action name
     *
     * @var string
     */
    var $action_name = 'Uploaded';
    
    /**
     * New file uploads have acitivity logs
     *
     * @var boolean
     */
    var $has_body = true;
    
    /**
     * Flag to render footer
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
      return get_image_url('activity_log/upload.gif', FILES_MODULE);
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
        
        $lang_params = array(
          'user_name'     => $created_by->getDisplayName(true), 
          'user_url' => $created_by->getViewUrl(),
          'url'      => $object->getViewUrl(),
        );
        
        if ($in_project) {
          return lang('<a href=":user_url">:user_name</a> uploaded a new file', $lang_params);
        } // if
        
        $project = $object->getProject();
        $lang_params['project_name'] = $project->getName();
        $lang_params['project_view_url'] = $project->getOverviewUrl();
        return lang('<a href=":user_url">:user_name</a> uploaded a new file to <a href=":project_view_url">:project_name</a> project', $lang_params);
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
      if($object === null) {
        $object = $this->getObject();
      } // if
      
      require_once SMARTY_PATH . '/plugins/modifier.filesize.php';
      $result = '<div class="file_details">';      
      $result .= '<div class="file_thumbnail"><a href="' . $object->getViewUrl() . '"><img src="' . $object->getThumbnailUrl() . '" alt="" /></a></div>';
      $result .= '<div class="file_name"><a href="' . $object->getViewUrl() . '">' . clean($object->getName()) . '</a>, ' . smarty_modifier_filesize($object->getSize()) . '</div>';
      
      if($object->getBody()) {
        $result .= '<div class="file_description">' . nl2br(str_excerpt(strip_tags($object->getBody()), 250)) . '</div>';
      } // if
      
      $result .= '</div>';
      
      return $result;
    } // renderBody
    
    /**
     * Render Log Details
     *
     * @param File $object
     * @param boolean $in_project
     */
    function renderFooter($object = null, $in_project = false) {
      if($object === null) {
        $object = $this->getObject();
      } // if
      
      $links = array();
      
      if($object->isImage()) {
        $links[] = '<a href="' . $object->getDownloadUrl() . '" target="_blank">' . lang('View') . '</a>';
      } // if
      
      $links[] = '<a href="' . $object->getDownloadUrl(true) . '">' . lang('Download') . '</a>';
      
      return implode(' &middot; ', $links);
    } // renderFooter
    
  }

?>