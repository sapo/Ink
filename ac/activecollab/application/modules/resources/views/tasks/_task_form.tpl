<div class="form_full_view">
  {wrap field=body}
    {label for=taskSummary required=yes}Summary{/label}
    {text_field name='task[body]' value=$task_data.body class='title required' id=taskSummary}
  {/wrap}
  
  <div class="col">
  {wrap field=priority}
    {label for=taskPriority}Priority{/label}
    {select_priority name='task[priority]' value=$task_data.priority id=taskPriority}
  {/wrap}
  </div>
  
  <div class="col">
  {wrap field=due_on}
    {label for=taskDueOn}Due on{/label}
    {select_date name='task[due_on]' value=$task_data.due_on id=taskDueOn}
  {/wrap}
  </div>
  
  <div class="col">
  {wrap field=assignees}
    {label for=taskAssignees}Assignees{/label}
    {select_assignees name='task[assignees]' value=$task_data.assignees object=$active_task project=$active_project}
    <div class="clear"></div>
  {/wrap}
  </div>
  
  <div class="clear"></div>
</div>