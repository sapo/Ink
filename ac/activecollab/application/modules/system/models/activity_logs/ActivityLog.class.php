<?php

  /**
   * ActivityLog class
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ActivityLog extends BaseActivityLog {
    
    /**
     * Cached parent object instance
     *
     * @var ProjectObject
     */
    var $object = false;
    
    /**
     * Indicates whether this activity log renders body information or not
     *
     * @var boolean
     */
    var $has_body = false;
    
    /**
     * Indicates whether this activity log renders footer information or not
     *
     * @var boolean
     */
    var $has_footer = false;
    
    /**
     * Action name
     *
     * @var string
     */
    var $action_name = false;

    /**
     * Return parent object
     *
     * @param void
     * @return ProjectObject
     */
    function getObject() {
      if($this->object === false) {
        $this->object = ProjectObjects::findById($this->getObjectId());
      } // if
      return $this->object;
    } // getObject
    
    /**
     * Cached project instance
     *
     * @var Project
     */
    var $project = false;
    
    /**
     * Return parent project
     *
     * @param void
     * @return Project
     */
    function getProject() {
      if($this->project === false) {
        $object = $this->getObject();
        if(instance_of($object, 'ProjectObject')) {
          return $object->getProject();
        } // if
      } // if
      return null;
    } // getProject
    
    /**
     * Strip entities from the comment
     *
     * @param void
     * @return string
     */
    function getComment() {
      return str_replace(array('&quot;', '&nbsp;'), array('"', ' '), parent::getComment());
    } // getComment
    
    /**
     * Return log icon URL
     *
     * @param void
     * @return string
     */
    function getIconUrl() {
      return get_image_url('activity_log/default.gif');
    } // getIconUrl
    
    /**
     * Add new entry to the log
     *
     * @param ProjectObject $object
     * @param User $by
     * @param string $comment
     * @return null
     */
    function log($object, $by = null, $comment = null) {
      $this->setType(get_class($this));
      $this->setObjectId($object->getId());
      $this->setProjectId($object->getProjectId());
      
      if($by === null) {
        $by = get_logged_user();
      } // if
      
      $this->setCreatedBy($by);
      $this->setCreatedOn(new DateTimeValue());
      
      if($comment) {
        $this->setComment($comment);
      } // if
      
      return $this->save();
    } // log
    
    // ---------------------------------------------------
    //  Renderers
    // ---------------------------------------------------
    
    /**
     * Render log details
     *
     * @param ProjectObject $object
     * @param boolean $in_project
     * @return string
     */
    function renderHead($object = null, $in_project = false) {
      use_error('NotImplementedError');
      return new NotImplementedError('ActivityLog', 'renderHead');
    } // renderHead
    
    /**
     * Render log details
     * 
     * @param ProjectObject $object
     * @param boolean $in_project
     * @return string
     */
    function renderBody($object = null, $in_project = false) {
      use_error('NotImplementedError');
      return new NotImplementedError('ActivityLog', 'renderBody');
    } // renderBody
    
    /**
     * Render log footer
     * 
     * @param ProjectObject $object
     * @param boolean $in_project
     * @return string
     */
    function renderFooter($object = null, $in_project = false) {
      use_error('NotImplementedError');
      return new NotImplementedError('ActivityLog', 'renderFooter');
    } // renderFooter
    
    /**
     * Render log for mobile devices
     *
     * @param ProjectObject $object
     * @param boolean $in_project
     * @return string
     */
    function renderMobile($object = null, $in_project = false) {
      if ($object === null) {
        $object = $this->getObject();
      } // if
      require_once(SMARTY_PATH.'/plugins/modifier.date.php');
      
      if (instance_of($object, 'ProjectObject')) {
        $return_string.=
        '<a href="' . mobile_access_module_get_view_url($object) . '">' .
          '<span class="object_type">' . $object->getVerboseType() . '</span>' .
          '<span class="main_link"><span>' . str_excerpt(clean($object->getName()), 40) . '</span></span>' .
          '<span class="details">'.ucfirst(lang($this->action_name)).' ' . lang('by') . ' <strong>' . clean($this->getCreatedByName()) . '</strong><br/>' . smarty_modifier_date($this->getCreatedOn()) . '</span>' .
        '</a>';
      }
      return $return_string;
    } // renderMobile
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Save log entry into the database
     *
     * @param void
     * @return boolean
     */
    function save() {
      if($this->isNew()) {
        $this->setType(get_class($this));
      } // if
      
      return parent::save();
    } // save
   
    /**
     * Validation
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if(!$this->validatePresenceOf('type')) {
        $errors->addError(lang('Activity log type is required'));
      } // if
      
      if(!$this->validatePresenceOf('object_id')) {
        $errors->addError(lang('Object ID is required'), 'object_id');
      } // if
    } // validate    
  }

?>