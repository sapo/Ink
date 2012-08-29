<div class="form_left_col">
  {wrap field=name}
    {label for=ticketSummary required=yes}Summary{/label}
    {text_field name='ticket[name]' value=$ticket_data.name id=ticketSummary class='title required validate_minlength 3'}
  {/wrap}
  
  {wrap field=body}
    {label for=ticketBody}Full description{/label}
    {editor_field name='ticket[body]' id=ticketBody inline_attachments=$ticket_data.inline_attachments}{$ticket_data.body}{/editor_field}
  {/wrap}
  
  {if $active_ticket->isNew()}
    <div class="ctrlHolderContainer">
      <a href="#" class="ctrlHolderToggler button_add attachments">{lang}Attach Files{/lang}...</a>
      <div class="strlHolderToggled">
      {wrap field=attachments}
        {label}Attachments{/label}
        {attach_files max_files=5}
      {/wrap}
      </div>
    </div>
  {/if}
  
  {wrap field=assignees}
    {label for=ticketAssignees}Assignees{/label}
    {select_assignees_inline name='ticket[assignees]' value=$ticket_data.assignees object=$active_ticket project=$active_project choose_responsible=true}
  {/wrap}
</div>

<div class="form_right_col">
  {wrap field=parent_id}
    {label for=ticketParent}Category{/label}
    {select_category name='ticket[parent_id]' value=$ticket_data.parent_id id=ticketParent module=tickets controller=tickets project=$active_project user=$logged_user}
  {/wrap}

{if $logged_user->canSeeMilestones($active_project)}
  {wrap field=milestone_id}
    {label for=ticketMilestone}Milestone{/label}
    {select_milestone name='ticket[milestone_id]' value=$ticket_data.milestone_id project=$active_project id=ticketMilestone}
  {/wrap}
{/if}

  {wrap field=priority}
    {label for=ticketPriority}Priority{/label}
    {select_priority name='ticket[priority]' value=$ticket_data.priority id=ticketPriority}
  {/wrap}
  
  {wrap field=tags}
    {label for=ticketTags}Tags{/label}
    {select_tags name='ticket[tags]' value=$ticket_data.tags project=$active_project id=ticketTags}
  {/wrap}
  
  {wrap field=due_on}
    {label for=ticketDueOn}Due on{/label}
    {select_date name='ticket[due_on]' value=$ticket_data.due_on id=ticketDueOn}
  {/wrap}
  
  {if $logged_user->canSeePrivate()}
    {wrap field=visibility}
      {label for=ticketVisibility}Visibility{/label}
      {select_visibility name=ticket[visibility] value=$ticket_data.visibility project=$active_project short_description=true}
    {/wrap}
  {else}
    <input type="hidden" name="ticket[visibility]" value="1" />
  {/if}
</div>

<div class="clear"></div>