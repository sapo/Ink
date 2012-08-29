{title id=$active_ticket->getTicketId() name=$active_ticket->getName()}Ticket #:id: :name{/title}
{page_object object=$active_ticket}
{add_bread_crumb}Details{/add_bread_crumb}

{object_quick_options object=$active_ticket user=$logged_user}
<div class="ticket main_object" id="ticket{$active_ticket->getId()}">
  <div class="body">
    <dl class="properties">
      <dt>{lang}Status{/lang}</dt>
    {if $active_ticket->isCompleted()}
      <dd>{action_on_by user=$active_ticket->getCompletedBy() datetime=$active_ticket->getCompletedOn() action=Completed}</dd>
    {else}
      <dd>{lang}Open{/lang}</dd>
    {/if}
    
      <dt>{lang}Priority{/lang}</dt>
      <dd>{$active_ticket->getFormattedPriority()}</dd>
      
    {if $active_ticket->getDueOn()}
      <dt>{lang}Due on{/lang}</dt>
      <dd>{$active_ticket->getDueOn()|date:0}</dd>
    {/if}
      
    {if $active_ticket->hasAssignees()}
      <dt>{lang}Assignees{/lang}</dt>
      <dd>{object_assignees object=$active_ticket}</dd>
    {/if}
    
    {if $logged_user->canSeeMilestones($active_project) && $active_ticket->getMilestoneId()}
      <dt>{lang}Milestone{/lang}</dt>
      <dd>{milestone_link object=$active_ticket}</dd>
    {/if}
      
    {if module_loaded('timetracking') && $logged_user->getProjectPermission('timerecord', $active_project)}
      <dt>{lang}Time{/lang}</dt>
      <dd>{object_time object=$active_ticket}</dd>
    {/if}
    
    {if $active_ticket->hasTags()}
      <dt>{lang}Tags{/lang}</dt>
      <dd>{object_tags object=$active_ticket}</dd>
    {/if}
    </dl>
  
  {if $active_ticket->getBody()}
    <div class="body content" id="ticket_body_{$active_ticket->getId()}">{$active_ticket->getFormattedBody()}</div>
    {if $active_ticket->getSource() == $smarty.const.OBJECT_SOURCE_EMAIL}
      <script type="text/javascript">
        App.EmailObject.init('ticket_body_{$active_ticket->getId()}');
      </script>
    {/if}
  {else}
    <div class="body content details">{lang}Full description for this ticket is not provided{/lang}</div>
  {/if}
  </div>
  
  <div class="resources">
    {object_attachments object=$active_ticket}
    {object_subscriptions object=$active_ticket}
    {object_tasks object=$active_ticket}
    
    <div class="resource object_comments" id="comments">
      <div class="body">
      {if $pagination->getLastPage() > 1}
        <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{$active_ticket->getViewUrl('-PAGE-')}{/pagination}</span></p>
        <div class="clear"></div>
        {/if}
        
        {if $pagination->getLastPage() > $pagination->getCurrentPage()}
          {object_comments object=$active_ticket comments=$comments show_header=no count_from=$count_start next_page=$active_ticket->getViewUrl($pagination->getNextPage())}
        {else}
          {object_comments object=$active_ticket comments=$comments show_header=no count_from=$count_start}
        {/if}
      </div>
    </div>
    
    {ticket_changes ticket=$active_ticket}
  </div>
</div>