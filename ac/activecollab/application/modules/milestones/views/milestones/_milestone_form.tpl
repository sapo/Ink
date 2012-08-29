<div class="form_left_col">
  {wrap field=name}
    {label for=milestoneName required=yes}Summary{/label}
    {text_field name='milestone[name]' value=$milestone_data.name id=milestoneName class='title required validate_minlength 3'}
  {/wrap}
  
  {if $active_milestone->isNew()}
    {wrap field=date_range}
      <div class="col">
      {wrap field=start_on}
        {label for=milestoneStartOn required=yes}Start on{/label}
        {select_date name='milestone[start_on]' value=$milestone_data.start_on id=milestoneStartOn  class=required}
      {/wrap}
      </div>
      
      <div class="col">
      {wrap field=due_on}
        {label for=milestoneDueOn required=yes}Due on{/label}
        {select_date name='milestone[due_on]' value=$milestone_data.due_on id=milestoneDueOn class=required}
      {/wrap}
      </div>
    {/wrap}
  {/if}
  
  {wrap field=body}
    {label for=milestoneBody}Notes{/label}
    {editor_field name='milestone[body]' id=milestoneBody inline_attachments=$milestone_data.inline_attachments}{$milestone_data.body}{/editor_field}
  {/wrap}
  
  {wrap field=assignees}
    {label for=milestoneAssignees}Assignees{/label}
    {select_assignees_inline name='milestone[assignees]' value=$milestone_data.assignees object=$active_milestone project=$active_project choose_responsible=true}
  {/wrap}
</div>

<div class="form_right_col">
  {wrap field=priority}
    {label for=milestonePriority}Priority{/label}
    {select_priority name='milestone[priority]' value=$milestone_data.priority id=milestonePriority}
  {/wrap}
  
  {wrap field=tags}
    {label for=milestoneTags}Tags{/label}
    {select_tags name='milestone[tags]' value=$milestone_data.tags project=$active_project id=milestoneTags}
  {/wrap}
</div>
<div class="clear"></div>