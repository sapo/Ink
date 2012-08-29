<?php

  /**
   * Single ticket change
   *
   * @package activeCollab.modules.tickets
   * @subpackage models
   */
  class TicketChange extends AngieObject {
    
    /**
     * Parent ticket ID
     * 
     * @var integer
     */
    var $ticket_id;
    
    /**
     * Ticket version
     * 
     * @var integer
     */
    var $version;
    
    /**
     * Associative array of ticket changes
     * 
     * @var array
     */
    var $changes = array();
    
    /**
     * Time when this change record is created
     * 
     * @var DateTimeValue
     */
    var $created_on;
    
    /**
     * Cached user object of person who created this change
     * 
     * @var User
     */
    var $created_by = false;
    
    /**
     * ID of user who created this entry
     * 
     * @var integer
     */
    var $created_by_id;
    
    /**
     * Name of the person who created this change
     * 
     * @var string
     */
    var $created_by_name;
    
    /**
     * Email of the person who created this change
     * 
     * @var string
     */
    var $created_by_email;
    
    /**
     * Return associative array of changes that is easy to display
     *
     * @param void
     * @return array
     */
    function getVerboseChanges() {
      $result = array();
      if(is_foreachable($this->getChanges())) {
        foreach($this->getChanges() as $field => $change_data) {
          list($old_value, $new_value) = $change_data;
          
          $log_fields = array('project_id', 'milestone_id', 'parent_id', 'name', 'body', 'priority', 'due_on');
          
          switch($field) {
            case 'project_id':
              $old_project = Projects::findById($old_value);
              $new_project = Projects::findById($new_value);
              
              $old_project_name = instance_of($old_project, 'Project') ? $old_project->getName() : lang('unknown project');
              $new_project_name = instance_of($new_project, 'Project') ? $new_project->getName() : lang('unknown project');
              
              $result[] = lang('Moved from <span>:from</span> to <span>:to</span>', array('from' => $old_project_name, 'to' => $new_project_name));
              
              break;
            case 'milestone_id':
              $old_milestone = Milestones::findById($old_value);
              $new_milestone = Milestones::findById($new_value);
              
              $old_milestone_name = instance_of($old_milestone, 'Milestone') ? $old_milestone->getName() : lang('-- none --'); 
              $new_milestone_name = instance_of($new_milestone, 'Milestone') ? $new_milestone->getName() : lang('-- none --'); 
              
              $result[] = lang('Moved from <span>:from</span> to <span>:to</span> milestone', array('from' => $old_milestone_name, 'to' => $new_milestone_name));
              
              break;
            case 'parent_id':
              $old_parent = ProjectObjects::findById($old_value);
              $new_parent = ProjectObjects::findById($new_value);
              
              $old_parent_name = instance_of($old_parent, 'Category') ? $old_parent->getName() : lang('-- none --');
              $new_parent_name = instance_of($new_parent, 'Category') ? $new_parent->getName() : lang('-- none --');
              
              $result[] = lang('Moved from <span>:from</span> to <span>:to</span> category', array('from' => $old_parent_name, 'to' => $new_parent_name));
              
              break;
            case 'name':
              $result[] = lang('Summary is changed from <span>:from</span> to <span>:to</span>', array('from' => $old_value, 'to' => $new_value));
              break;
            case 'body':
              $result[] = lang('Long description is changed');
              break;
            case 'priority':
              switch($old_value) {
                case PRIORITY_HIGHEST:
                  $old_priority = lang('Highest');
                  break;
                case PRIORITY_HIGH:
                  $old_priority = lang('High');
                  break;
                case PRIORITY_NORMAL:
                  $old_priority = lang('Normal');
                  break;
                case PRIORITY_LOW:
                  $old_priority = lang('Low');
                  break;
                case PRIORITY_LOWEST:
                  $old_priority = lang('Lowest');
                  break;
              } // switch
              
              switch($new_value) {
                case PRIORITY_HIGHEST:
                  $new_priority = lang('Highest');
                  break;
                case PRIORITY_HIGH:
                  $new_priority = lang('High');
                  break;
                case PRIORITY_NORMAL:
                  $new_priority = lang('Normal');
                  break;
                case PRIORITY_LOW:
                  $new_priority = lang('Low');
                  break;
                case PRIORITY_LOWEST:
                  $new_priority = lang('Lowest');
                  break;
              } // switch
              
              $result[] = lang('Priority is changed from <span>:from</span> to <span>:to</span>', array('from' => $old_priority, 'to' => $new_priority));
              break;
            case 'due_on':
              require_once SMARTY_PATH . '/plugins/modifier.date.php';
              
              $old_due_on = instance_of($old_value, 'DateValue') ? smarty_modifier_date($old_value, 0) : lang('-- none --');
              $new_due_on = instance_of($new_value, 'DateValue') ? smarty_modifier_date($new_value, 0) : lang('-- none --');
              
              $result[] = lang('Due date is changed from <span>:from</span> to <span>:to</span>', array('from' => $old_due_on, 'to' => $new_due_on));
              break;
            case 'completed_on':
              if(instance_of($old_value, 'DateValue') && ($new_value === null)) {
                $result[] = lang('Status changed to: Open');
              } elseif(($old_value === null) && instance_of($new_value, 'DateValue')) {
                $result[] = lang('Status changed to: Completed');
              } // if
              break;
            case 'owner':
              if($new_value) {
                $new_owner = Users::findById($new_value);
                if(instance_of($new_owner, 'User')) {
                  $result[] = lang(':user is responsible', array('user' => $new_owner->getDisplayName()));;
                } else {
                  $result[] = lang('Owner changed (unknown user or deleted in the meantime)');
                } // if
              } else {
                $result[] = lang('Anyone can pick up and work on this ticket');
              } // if
              break;
            case 'assignees':
              $old_assignees = array();
              if(is_foreachable($old_value)) {
                $old_assignees_users = Users::findByIds($old_value);
                if(is_foreachable($old_assignees_users)) {
                  foreach($old_assignees_users as $user) {
                  	$old_assignees[$user->getId()] = $user->getDisplayName();
                  } // foreach
                } // if
              } // if
              
              $new_assignees = array();
              if(is_foreachable($new_value)) {
                $new_assignees_users = Users::findByIds($new_value);
                if(is_foreachable($new_assignees_users)) {
                  foreach($new_assignees_users as $user) {
                  	$new_assignees[$user->getId()] = $user->getDisplayName();
                  } // foreach
                } // if
              } // if
              
              foreach($new_assignees as $new_assignee_id => $new_assignee) {
                if(!array_key_exists($new_assignee_id, $old_assignees)) {
                  $result[] = lang(':user has been assigned to this ticket', array('user' => $new_assignee));
                } // if
              } // foreach
              
              foreach($old_assignees as $old_assignee_id => $old_assignee) {
                if(!array_key_exists($old_assignee_id, $new_assignees)) {
                  $result[] = lang(':user has been removed from this ticket', array('user' => $old_assignee));
                } // if
              } // foreach
              
              break;
          } // switch
        } // foreach
      } // if
      return $result;
    } // getVerboseChanges
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
    * Get value of ticket_id
    *
    * @param void
    * @return integer
    */
    function getTicketId() {
      return $this->ticket_id;
    } // getTicketId
    
    /**
    * Set value of ticket_id
    *
    * @param integer $value
    * @return null
    */
    function setTicketId($value) {
      $this->ticket_id = $value;
    } // setTicketId
    
    /**
    * Get value of version
    *
    * @param void
    * @return integer
    */
    function getVersion() {
      return $this->version;
    } // getVersion
    
    /**
    * Set value of version
    *
    * @param integer $value
    * @return null
    */
    function setVersion($value) {
      $this->version = (integer) $value;
    } // setVersion
    
    /**
     * Return array of changes
     *
     * @param void
     * @return array
     */
    function getChanges() {
      return $this->changes;
    } // getChanges
    
    /**
     * Add change to the record
     *
     * @param string $field
     * @param mixed $old_value
     * @param mixed $new_value
     * @return null
     */
    function addChange($field, $old_value, $new_value) {
      $this->changes[$field] = array($old_value, $new_value);
    } // addChange
    
    /**
    * Get value of created_on
    *
    * @param void
    * @return DateTimeValue
    */
    function getCreatedOn() {
      return $this->created_on;
    } // getCreatedOn
    
    /**
    * Set value of created_on
    *
    * @param DateTimeValue $value
    * @return null
    */
    function setCreatedOn($value) {
      $this->created_on = $value;
    } // setCreatedOn
    
    /**
    * Get value of created_by
    *
    * @param void
    * @return User
    */
    function getCreatedBy() {
      if($this->created_by === false) {
        $created_by_id = $this->created_by_id;
        
        if($created_by_id) {
          $this->created_by = Users::findById($created_by_id);
        } else {
          $this->created_by = new AnonymousUser($this->created_by_name, $this->created_by_email);
        } // if
      } // if
      return $this->created_by;
    } // getCreatedBy
    
    /**
    * Set value of created_by
    *
    * @param User $value
    * @return null
    */
    function setCreatedBy($value) {
      if($value === null) {
        $this->created_by_id = 0;
        $this->created_by_name = '';
        $this->created_by_email = '';
      } elseif(instance_of($value, 'User')) {
        $this->created_by_id = $value->getId();
        $this->created_by_name = $value->getDisplayName();
        $this->created_by_email = $value->getEmail();
      } elseif(instance_of($value, 'AnonymousUser')) {
        $this->created_by_id = 0;
        $this->created_by_name = $value->getName();
        $this->created_by_email = $value->getEmail();
      } // if
    } // setCreatedBy
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Save to database
     *
     * @param void
     * @return boolean
     */
    function save() {
      if(is_foreachable($this->changes)) {
        return db_execute(sprintf('INSERT INTO ' . TABLE_PREFIX . 'ticket_changes (ticket_id, version, changes, created_on, created_by_id, created_by_name, created_by_email) VALUES (%s, %s, %s, %s, %s, %s, %s)', 
          db_escape($this->getTicketId()), db_escape($this->getVersion()), db_escape(serialize($this->changes)), db_escape($this->created_on), db_escape($this->created_by_id), db_escape($this->created_by_name), db_escape($this->created_by_email)
        ));
      } // if
      return true;
    } // save
    
  } // TicketChange

?>