{if !$show_only_tasks}
  {page_object object=$active_checklist}
  {add_bread_crumb}Details{/add_bread_crumb}
  
  {object_quick_options object=$active_checklist user=$logged_user}
  <div class="checklist main_object" id="checklist{$active_checklist->getId()}">
    <div class="body">
      <dl class="properties">
        <dt>{lang}Status{/lang}</dt>
      {if $active_checklist->isCompleted()}
        <dd>{action_on_by user=$active_checklist->getCompletedBy() datetime=$active_checklist->getCompletedOn() action=Completed}</dd>
      {else}
        <dd>{lang}Open{/lang}</dd>
      {/if}
      
      {if $logged_user->canSeeMilestones($active_project) && $active_checklist->getMilestoneId()}
        <dt>{lang}Milestone{/lang}</dt>
        <dd>{milestone_link object=$active_checklist}</dd>
      {/if}
      {if $active_checklist->hasTags()}
        <dt>{lang}Tags{/lang}</dt>
        <dd>{object_tags object=$active_checklist}</dd>
      {/if}
      </dl>
    </div>
    
    {if $active_checklist->getBody()}
    <div class="body content">{$active_checklist->getFormattedBody()}</div>
    {else}
    <div class="body content details">{lang}Description for this checklist is not provided{/lang}</div>
    {/if}
    
    <div class="resources">
      {object_tasks object=$active_checklist force_show=yes}
    </div>
    <div class="clear"></div>
  </div>
{else}
  {object_tasks object=$active_checklist force_show=yes skip_head=true}
{/if}