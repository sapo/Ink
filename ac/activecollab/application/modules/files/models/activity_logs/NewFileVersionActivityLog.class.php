<?php

  /**
   * New file version posted activity log entry
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class NewFileVersionActivityLog extends ActivityLog {
    /**
     * Action name
     *
     * @var string
     */
    var $action_name = 'New version';
    
    
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
     * Load from row
     *
     * @param array $row
     * @return null
     */
    function loadFromRow($row) {
      $result = parent::loadFromRow($row);
      if($result && (integer) $this->getComment() > 0) {
        $this->has_body = true;
        $this->has_footer = true;
      } // if
      return $result;
    } // loadFromRow
    
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
        
        $attachment = $this->getAttachment();
        if(instance_of($attachment, 'Attachment')) {
          $lang_params = array(
            'name' => $object->getName(),
            'url' => $object->getViewUrl(),
            'user_name'     => $created_by->getDisplayName(true), 
            'user_url' => $created_by->getViewUrl(),
            'url'      => $object->getViewUrl(),
          );
          
          if ($in_project) {
            return lang('<a href=":user_url">:user_name</a> uploaded a new version of <a href=":url">:name</a> file', $lang_params);
          } // if

          $project = $object->getProject();
          $lang_params['project_name'] = $project->getName();
          $lang_params['project_view_url'] = $project->getOverviewUrl();
          return lang('<a href=":user_url">:user_name</a> uploaded a new version of <a href=":url">:name</a> file in <a href=":project_view_url">:project_name</a> project', $lang_params);
        } else {
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
      $attachment = $this->getAttachment();
      if(instance_of($attachment, 'Attachment')) {
        if($object === null) {
          $object = $this->getObject();
        } // if
        
        require_once SMARTY_PATH . '/plugins/modifier.filesize.php';
        $result = '<div class="file_details">';      
        $result .= '<div class="file_thumbnail"><img src="' . $attachment->getThumbnailUrl() . '" alt="" /></div>';
        $result .= '<div class="file_name"><a href="' . $attachment->getViewUrl() . '">' . clean($object->getName()) . '</a>, ' . smarty_modifier_filesize($attachment->getSize()) . '</div>';
        
        $result .= '</div>';
        
        return $result;
      } else {
        return '';
      } // if
    } // renderBody
    
    /**
     * Render Log Details
     *
     * @param ProjectObject $object
     * @param boolean $in_project
     */
    function renderFooter($object = null, $in_project = false) {
      $links = array();
      
      if(instance_of($object,  'File')) {
        $links[] = lang('<a href=":download_url">Download</a>', array('download_url' => $object->getDownloadUrl()));
      } // if
      
      return implode(' &middot; ', $links);
    } // renderFooter
    
    /**
     * Version attachment instance
     *
     * @var Attachment
     */
    var $attachment = false;
    
    /**
     * Return last version attachment object
     *
     * @param void
     * @return Attachment
     */
    function getAttachment() {
      if($this->attachment === false) {
        $attachment_id = (integer) $this->getComment();
        if($attachment_id) {
          $this->attachment = Attachments::findById($attachment_id);
        } // if
      } // if
      return $this->attachment;
    } // getAttachment
    
  }

?>