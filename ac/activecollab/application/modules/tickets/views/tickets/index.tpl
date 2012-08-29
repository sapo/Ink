{title}Tickets{/title}
{if $active_category->isLoaded()}
  {add_bread_crumb}Open Tickets{/add_bread_crumb}
{else}
  {add_bread_crumb}All Open Tickets{/add_bread_crumb}
{/if}

<div class="list_view small_list_view">
  <div id="tickets" class="object_list">
  {if is_foreachable($groupped_tickets)}
    {form action=$mass_edit_tickets_url method=post}
      {foreach from=$groupped_tickets item=tickets_by_milestone}
        {if is_foreachable($tickets_by_milestone.objects)}
        
          <!-- Header -->
          {if $logged_user->canSeeMilestones($active_project)}
            {if instance_of($tickets_by_milestone.milestone, 'Milestone')}
              {if $can_add_ticket}
                <h2 class="section_name"><span class="section_name_span">
                  <span class="section_name_span_span">{object_link object=$tickets_by_milestone.milestone}</span>
                  <ul class="section_options">
                    {assign_var name=add_ticket_to_milestone_url}{assemble route=project_tickets_add project_id=$active_project->getId() milestone_id=$tickets_by_milestone.milestone->getId()}{/assign_var}
                    <li><a href="{$add_ticket_to_milestone_url}">{lang}Add Ticket{/lang}</a></li>
                  </ul>
                  <div class="clear"></div>
                </span></h2>
              {else}
                <h2 class="section_name"><span class="section_name_span">{object_link object=$tickets_by_milestone.milestone}</span></h2>
              {/if}
            {else}
              <h2 class="section_name"><span class="section_name_span">{lang}Unknown Milestone{/lang}</span></h2>
            {/if}
          {/if}
                    
          <div class="section_container visible_overflow">
            {if instance_of($tickets_by_milestone.milestone, 'Milestone')}
              {assign_var name=reorder_tickets_url}{assemble route=project_tickets_reorder project_id=$active_project->getId() milestone_id=$tickets_by_milestone.milestone->getId() async=1}{/assign_var}
              {assign_var name=milestone_name}{$tickets_by_milestone.milestone->getName()}{/assign_var}
            {else}
              {assign_var name=reorder_tickets_url}{assemble route=project_tickets_reorder project_id=$active_project->getId() milestone_id=0 async=1}{/assign_var}
              {assign_var name=milestone_name}Unknown{/assign_var}
            {/if}
            <ul class="tickets_list common_table_list" reorder_url='{$reorder_tickets_url}'>
            {foreach from=$tickets_by_milestone.objects item=ticket}
              <li class="ticket {cycle values='odd,even' name=$milestone_name} sort" id="ticket{$ticket->getId()}">
                <span class="left_options">
                  <span class="option star">{object_star object=$ticket user=$logged_user}</span>
                  <span class="option">{object_priority object=$ticket}</span>
                  <span class="option ticket_id">#{$ticket->getTicketId()}</span>
                </span>
                <span class="right_options">
                {if $logged_user->canSeePrivate()}
                  <span class="option">{object_visibility object=$ticket user=$logged_user}</span>
                {/if}
                  <span class="option"><input type="checkbox" name="tickets[]" value="{$ticket->getId()}" class="auto input_checkbox" /></span>
                </span>
                <span class="main_data">
                  <a href="{$ticket->getViewUrl()}">{$ticket->getName()|clean}</a>
                  <input type="hidden" name="reorder_ticket[{$ticket->getId()}]" />
                </span>
              </li>
              {/foreach}
              <li class="empty_row" style="{if is_foreachable($tickets_by_milestone.objects)}display: none;{/if}">{lang}There are no tickets in this milestone{/lang}</li>
            </ul>
          </div>
        {/if}
      {/foreach}
      
      <div id="mass_edit">
        <select name="with_selected" id="tickets_action" class="auto">
          <option value="">{lang}With Selected ...{/lang}</option>
          <option value=""></option>
          <option value="complete">{lang}Mark as Completed{/lang}</option>
          <option value=""></option>
          <option value="star">{lang}Star{/lang}</option>
          <option value="unstar">{lang}Unstar{/lang}</option>
          <option value=""></option>
          <option value="trash">{lang}Move to Trash{/lang}</option>
          <option value=""></option>
          
          <optgroup label="{lang}Visibility{/lang}">
            <option value="set_visibility_0">{lang}Private{/lang}</option>
            <option value="set_visibility_1">{lang}Normal{/lang}</option>
          </optgroup>
          <option value=""></option>
          
          <optgroup label="{lang}Change priority{/lang}">
            <option value="set_priority_2">{lang}Highest{/lang}</option>
            <option value="set_priority_1">{lang}High{/lang}</option>
            <option value="set_priority_0">{lang}Normal{/lang}</option>
            <option value="set_priority_-1">{lang}Low{/lang}</option>
            <option value="set_priority_-2">{lang}Lowest{/lang}</option>
          </optgroup>
          
      {if $logged_user->canSeeMilestones($active_project) && is_foreachable($milestones)}
          <option value=""></option>
          <optgroup label="{lang}Move to milestone{/lang}">
            <option value="move_to_milestone">{lang}&lt;None&gt;{/lang}</option>
        {foreach from=$milestones item=milestone}
            <option value="move_to_milestone_{$milestone->getId()}">{$milestone->getName()|clean}</option>
        {/foreach}
          </optgroup>
      {/if}
      
          <option value=""></option>
          <optgroup label="{lang}Move to category{/lang}">
            <option value="move_to_category">{lang}&lt;None&gt;{/lang}</option>
        {foreach from=$categories item=category}
            <option value="move_to_category_{$category->getId()}">{$category->getName()|clean}</option>
        {/foreach}
          </optgroup>
        </select>
        <button class="simple" id="tickets_submit" type="submit" class="auto">{lang}Go{/lang}</button>
      </div>
      <div class="clear"></div>
    {/form}
  {else}
    <p class="empty_page">{lang}No tickets here{/lang}. {if $add_ticket_url}{lang add_url=$add_ticket_url}Would you like to <a href=":add_url">create one</a>{/lang}?{/if}</p>
    {empty_slate name=tickets module=tickets}
  {/if}
  
    <p class="archive_link">{link href=$tickets_archive_url}Archive{/link}</p>
  </div>
  
  <ul class="category_list">
    <li {if $active_category->isNew()}class="selected"{/if}><a href="{$tickets_url}"><span>{lang}All Open Tickets{/lang}</span></a></li>
  {if is_foreachable($categories)}
    {foreach from=$categories item=category}
    <li category_id="{$category->getId()}" {if $active_category->isLoaded() && $active_category->getId() == $category->getId()}class="selected"{/if}><a href="{assemble route=project_tickets project_id=$active_project->getId() category_id=$category->getId()}"><span>{$category->getName()|clean}</span></a></li>
    {/foreach}
  {/if}
  {if $can_manage_categories}
    <li id="manage_categories"><a href="{$categories_url}"><span>{lang}Manage Categories{/lang}</span></a></li>
  {/if}
  </ul>
  <script type="text/javascript">
    App.resources.ManageCategories.init('manage_categories');
  </script>
  
  <div class="clear"></div>
</div>
