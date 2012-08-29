<?php

  /**
   * Resources handle on_project_object_reassigned even
   *
   * @package activeCollab.modules.resources
   * @subpackage handlers
   */

  /**
   * Handle on_project_object_reassigned event
   *
   * @param ProjectObject $object
   * @param array $old_assignment_data
   * @param array $new_assignment_data
   * @return null
   */
  function resources_handle_on_project_object_reassigned(&$object, $old_assignment_data, $new_assignment_data) {
  	if(is_array($old_assignment_data)) {
  	  list($old_assignees, $old_owner_id) = $old_assignment_data;
  	} else {
  	  $old_assignees = array();
  	  $old_owner_id = 0;
  	} // if
  	
  	if(is_array($new_assignment_data)) {
  	  list($new_assignees, $new_owner_id) = $new_assignment_data;
  	} else {
  	  $new_assignees = array();
  	  $new_owner_id = 0;
  	} // if
  	
  	// ---------------------------------------------------
  	//  Collect user data
  	// ---------------------------------------------------
  	
  	$all_user_ids = array();
  	foreach($old_assignees as $assignee_id) {
  	  if(!in_array($assignee_id, $all_user_ids)) {
  	    $all_user_ids[] = $assignee_id;
  	  } // foreach
  	} // if
  	
  	foreach($new_assignees as $assignee_id) {
  	  if(!in_array($assignee_id, $all_user_ids)) {
  	    $all_user_ids[] = $assignee_id;
  	  } // foreach
  	} // if
  	
  	if(is_foreachable($all_user_ids)) {
  	  $all_users = Users::findByIds($all_user_ids);
  	} else {
  	  return;
  	} // if
  	
  	$user_map = array();
  	foreach($all_users as $user) {
  	  $user_map[$user->getId()] = $user->getDisplayName();
  	} // if
  	
  	// ---------------------------------------------------
  	//  Prepare changes array
  	// ---------------------------------------------------
  	
  	$changes = array();
  	
  	// Nobody assigned
  	if($new_owner_id == 0) {
  	  $changes[] = lang('Anyone can pick and complete this task');
  	  
  	  if($old_owner_id && isset($user_map[$old_owner_id])) {
  	    $changes[] = lang(':name is no longer responsible for this task', array('name' => $user_map[$old_owner_id]));
  	  } // if
  	  
  	  foreach($old_assignees as $assignee_id) {
  	    if(isset($user_map[$assignee_id])) {
  	      $changes[] = lang(':name has been removed from this task', array('name' => $user_map[$assignee_id]));
  	    } // if
  	  } // foreach
  	  
  	// We have new assignees
  	} else {
  	  if($old_owner_id != $new_owner_id) {
  	    if(isset($user_map[$new_owner_id])) {
  	      $changes[] = lang(':name is responsible for this task', array('name' => $user_map[$new_owner_id]));
  	    } // if
  	    
  	    if($old_owner_id && isset($user_map[$old_owner_id])) {
  	      $changes[] = lang(':name is no longer responsible for this task', array('name' => $user_map[$old_owner_id]));
  	    } // if
  	  } // if
  	  
  	  foreach($new_assignees as $assignee_id) {
  	    if(isset($user_map[$assignee_id]) && !in_array($assignee_id, $old_assignees)) {
  	      $changes[] = lang(':name has been added to this task', array('name' => $user_map[$assignee_id]));
  	    } // if
  	  } // foreach
  	  
  	  foreach($old_assignees as $assignee_id) {
  	    if(isset($user_map[$assignee_id]) && !in_array($assignee_id, $new_assignees)) {
  	      $changes[] = lang(':name has been removed from this task', array('name' => $user_map[$assignee_id]));
  	    } // if
  	  } // foreach
  	} // if
  	
  	if(is_foreachable($changes) && is_foreachable($all_users)) {
  	  $changes_body = "<p>\n";
  	  foreach($changes as $change) {
  	    $changes_body .= "- $change<br />\n";
  	  } // foreach
  	  $changes_body .= "</p>";
  	  
  	  $owner_company = get_owner_company();
      $project = $object->getProject();
      
      // Don't send email to person who made the change
      foreach($all_users as $k => $user) {
        if($user->getId() == $object->getUpdatedById()) {
          unset($all_users[$k]);
        } // if
      } // foreach
  	  
      if(is_foreachable($all_users)) {
    	  ApplicationMailer::send($all_users, 'resources/task_reassigned', array(
          'owner_company_name' => $owner_company->getName(),
          'project_name'       => $project->getName(),
          'project_url'        => $project->getOverviewUrl(),
          'object_type'        => $object->getTypeName(),
          'object_name'        => $object->getName(),
          'object_body'        => $object->getFormattedBody(),
          'object_url'         => $object->getViewUrl(),
          'changes_body'       => $changes_body,
    	  ), $object->getNotificationContext());
      } // if
  	} // if
  } // resources_handle_on_project_object_reassigned

?>