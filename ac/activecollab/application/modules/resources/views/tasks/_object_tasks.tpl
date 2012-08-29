{if !$_object_tasks_skip_wrapper}
<div class="resource object_tasks object_section" id="object_tasks_for_{$_object_tasks_object->getId()}" {if !$_object_tasks_force_show && !(is_foreachable($_object_tasks_open) || is_foreachable($_object_tasks_completed))}style="display: none"{/if}>
{/if}

  {if $_object_tasks_skip_head==false}
    <div class="head">
      {assign_var name=section_title}{lang}Tasks{/lang}{/assign_var}
      {if $_object_tasks_object->canSubtask($logged_user)}
        <h2 class="section_name"><span class="section_name_span">
          <span class="section_name_span_span">{$section_title}</span>
          <div class="clear"></div>
        </span></h2>
      {else}
        <h2 class="section_name"><span class="section_name_span">
          <span class="section_name_span_span">{$section_title}</span>
        </span></h2>
      {/if}
    </div>
  {/if}
  <div class="body">
  {form method='POST' action=$_object_tasks_object->getReorderTasksUrl(true) class='sort_form visible_overflow'}
  <ul class="tasks_table common_table_list highlight_priority open_tasks_table">
    {if is_foreachable($_object_tasks_open)}
      {foreach from=$_object_tasks_open item=_object_task}
        {include_template module=resources controller=tasks name=_task_opened_row}
      {/foreach}
    {/if}
    <li class="empty_row" style="{if is_foreachable($_object_tasks_open)}display: none{/if}">{lang object_type=$_object_tasks_object->getVerboseType()}There are no active Tasks in this :object_type{/lang}</li>
  </ul>
  {/form}
  {if $_object_tasks_object->canSubtask($logged_user)}
    <div class="hidden_overflow">
      <div class="add_task_form" style="display: none">
        {form action=$_object_tasks_object->getPostTaskUrl() method=post}
          <div class="columns">
            <div class="form_left_col">
              {wrap field=body}
                {label for=taskSummary required=yes}Summary{/label}
                {text_field name='task[body]' class='long required' id=taskSummary}
              {/wrap}
              
              <p class="show_due_date_and_priority"><a class="additional_form_links" href="#">{lang}Set priority and due date...{/lang}</a></p>
              
              <div class="due_date_and_priority">
                <div class="col_wide">
                {wrap field=priority}
                  {label for=taskPriority}Priority{/label}
                  {select_priority name='task[priority]' id=taskPriority}
                {/wrap}
                </div>
                
                <div class="col_wide2">
                {wrap field=due_on}
                  {label for=taskDueOn}Due on{/label}
                  {select_date name='task[due_on]' id=taskDueOn}
                {/wrap}
                </div>
              </div>
              <div class="clear"></div>
            </div>
            
            <div class="form_right_col">
              {wrap field=assignees}
                {label for=taskAssignees}Assignees{/label}
                {select_assignees name='task[assignees]' object=$_object_tasks_object project=$active_project}
              {/wrap}
            </div>
          </div>
          {wrap_buttons}
            {submit}Submit{/submit}
            <a href="#" class="text_button cancel_button">{lang}Done adding tasks?{/lang}</a>
          {/wrap_buttons}
        {/form}
      </div>
      <a href="{$_object_tasks_object->getPostTaskUrl()}" class="add_task_link button_add dont_print"><span>{lang}Add Another Task{/lang}</span></a>
    </div>
  {/if}

  <ul class="tasks_table common_table_list completed_tasks_table">
  {if is_foreachable($_object_tasks_completed)}
    {foreach from=$_object_tasks_completed item=_object_task}
      {include_template module=resources controller=tasks name=_task_completed_row}
    {/foreach}
    {if $_object_tasks_completed_remaining > 0}
      <li class="list_all_completed"><a href="{assemble route='project_tasks_list_completed' project_id=$active_project->getId() parent_id=$_object_tasks_object->getId()}">{lang remaining_count=$_object_tasks_completed_remaining}Show :remaining_count remaining completed tasks{/lang}</a></li>
    {/if}
  {/if}
  </ul>
  </div>
  
{if !$_object_tasks_skip_wrapper}
</div>
<script type="text/javascript">
  App.layout.init_object_tasks('object_tasks_for_{$_object_tasks_object->getId()}', '{$_object_tasks_can_reorder}');
</script>
{/if}