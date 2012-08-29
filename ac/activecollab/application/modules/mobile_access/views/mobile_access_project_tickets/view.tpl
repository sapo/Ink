<div class="wrapper">
  <div class="box">
    <div class="object_main_info">
       <h1 class="object_name">{$active_ticket->getName()|clean}</h1>
    </div>
    {mobile_access_object_properties object=$active_ticket show_completed_status=true show_tags=true show_body=true show_milestone=true show_priority=true show_total_time=true show_milestone_day_info=true show_assignees=true}
  </div>
  {mobile_access_object_tasks object=$active_ticket} 
  
  {mobile_access_object_comments object=$active_ticket user=$logged_user}
  {mobile_access_add_comment_form parent=$active_ticket comment_data=$comment_data}
  
  <h2 class="label">{lang}History{/lang}</h2>
  <div class="box">
    {ticket_changes ticket=$active_ticket}
  </div>  
</div>