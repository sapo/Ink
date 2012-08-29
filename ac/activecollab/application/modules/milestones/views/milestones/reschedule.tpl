{title}Reschedule milestone{/title}
{add_bread_crumb}Reschedule{/add_bread_crumb}

<div id="reschedule_milestone">
  {form action=$active_milestone->getRescheduleUrl() method=post id=reschedule_milestone_form}
    {wrap field=date_range}
      <div class="col">
      {wrap field=start_on}
        {label for=milestoneStartOn required=yes}Start on{/label}
        {select_date name='milestone[start_on]' value=$milestone_data.start_on id=milestoneStartOn}
      {/wrap}
      </div>
      
      <div class="col">
      {wrap field=due_on}
        {label for=milestoneDueOn required=yes}Due on{/label}
        {select_date name='milestone[due_on]' value=$milestone_data.due_on id=milestoneDueOn}
      {/wrap}
      </div>
    {/wrap}
    
    {wrap field=with_sucessive}
      {label}With Successive Milestones{/label}
      {with_successive_milestones name=milestone[with_sucessive] value=$milestone_data.with_sucessive milestone=$active_milestone}
    {/wrap}
    
    {wrap field=reschedule_milstone_objects}
      <input type="checkbox" name="milestone[reschedule_milstone_objects]" id="milestoneRescheduleTasks" class="inline input_checkbox" {if $milestone_data.reschedule_milstone_objects}checked="checked"{/if} /> {label class=inline for=milestoneRescheduleTasks}Also reschedule all tickets and tasks that belong to milestones you are rescheduling{/label}
    {/wrap}
  
    {wrap_buttons}
      {submit}Reschedule{/submit}
    {/wrap_buttons}
  {/form}
</div>