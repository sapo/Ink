{form action=$_post_task_form_object->getPostTaskUrl() method=post autofocus=no ask_on_leave=no}
  {wrap field=body}
    {text_field name='task[body]' value=$task_data.body}
  {/wrap}
  
  <div class="col">
  {wrap field=priority}
    {label for=taskPriority}Priority{/label}
    {select_priority name='task[priority]' value=$task_data.priority id=taskPriority}
  {/wrap}
  
  {wrap field=due_on}
    {label for=taskDueOn}Due on{/label}
    {select_date name='task[due_on]' value=$task_data.due_on id=taskDueOn}
  {/wrap}
  </div>
  
  <div class="col">
  {wrap field=assignees}
    {label for=taskAssignees}Assignees{/label}
    {select_assignees name='task[assignees]' value=$task_data.assignees object=$_post_task_form_object project=$active_project}
  {/wrap}
  </div>
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}