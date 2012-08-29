{page_object object=$active_milestone}
{add_bread_crumb}Details{/add_bread_crumb}

{object_quick_options object=$active_milestone user=$logged_user}
<div class="milestone main_object" id="milestone{$active_milestone->getId()}">
  <div class="body">
    <dl class="properties">
      <dt>{lang}Status{/lang}</dt>
    {if $active_milestone->isCompleted()}
      <dd>{action_on_by user=$active_milestone->getCompletedBy() datetime=$active_milestone->getCompletedOn() action='Completed'}</dd>
    {else}
      <dd>{lang}Active{/lang}</dd>
    {/if}
    
    {if $active_milestone->isDayMilestone()}
      <dt>{lang}Due On{/lang}</dt>
      <dd>{$active_milestone->getDueOn()|date:0}</dd>
    {else}
      <dt>{lang}From / To{/lang}</dt>
      <dd>{$active_milestone->getStartOn()|date:0} &mdash; {$active_milestone->getDueOn()|date:0}</dd>
    {/if}
      
      <dt>{lang}Priority{/lang}</dt>
      <dd>{$active_milestone->getFormattedPriority()|clean}</dd>
      
    {if $active_milestone->hasAssignees(true)}
      <dt>{lang}Assignees{/lang}</dt>
      <dd>{object_assignees object=$active_milestone}</dd>
    {/if}
      
    {if $active_milestone->hasTags()}
      <dt>{lang}Tags{/lang}</dt>
      <dd>{object_tags object=$active_milestone}</dd>
    {/if}
    
    {if $milestone_add_links_code}
      <dt>{lang}Add to Milestone{/lang}</dt>
      <dd>{$milestone_add_links_code}</dd>
    {/if}
    </dl>
  </div>
  
  {if $active_milestone->getBody()}
    <div class="body content">{$active_milestone->getFormattedBody()}</div>
  {else}
    <div class="body content details">{lang}No notes...{/lang}</div>
  {/if}
   
  <div class="resources">
    {if $total_objects && is_foreachable($milestone_objects)}
      {foreach from=$milestone_objects key=section_name item=objects}
      {if is_foreachable($objects)}
        <div class="resource">
          <div class="head">
            <h2 class="section_name"><span class="section_name_span">{$section_name}</span></h2>
          </div>
          <div class="body">
            {list_objects objects=$objects show_checkboxes=no show_header=no}
          </div>
        </div>
      {/if}
      {/foreach}
    {else}
      <div class="body content details">{lang}No tasks in this milestone...{/lang}</div>
    {/if}
   
    {object_subscriptions object=$active_milestone}
  </div>
  
</div>