{title}Archive{/title}
{add_bread_crumb not_lang=yes}{lang page=$pagination->getCurrentPage()}Page :page{/lang}{/add_bread_crumb}

<div class="list_view" id="milestones">
  <div class="object_list">
  {if is_foreachable($tickets)}
    {if $pagination->getLastPage() > 1}
      {if isset($active_category) && instance_of($active_category, 'Category') && $active_category->isLoaded()}
        <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=project_tickets_archive project_id=$active_project->getId() page='-PAGE-' category_id=$active_category->getId()}{/pagination}</span></p>
      {else}
        <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=project_tickets_archive project_id=$active_project->getId() page='-PAGE-'}{/pagination}</span></p>
      {/if}
    {/if}
    
    <div class="clear"></div>
    {form action=$mass_edit_tickets_url method=post}
      <table>
        <tbody>
        {foreach from=$tickets item=ticket}
          <tr class="discussion {cycle values='odd,even'}" id="ticket{$ticket->getId()}">
            <td class="star">{object_star object=$ticket user=$logged_user}</td>
            <td class="priority">{object_priority object=$ticket}</td>
            <td class="id">#{$ticket->getTicketId()}</td>
            <td class="name">
              {object_link object=$ticket}
              <span class="details block">{action_on_by user=$ticket->getCompletedBy() datetime=$ticket->getCompletedOn() action=Completed}</span>
            </td>
            <td class="ticket_checkbox"><input type="checkbox" name="tickets[]" value="{$ticket->getId()}" class="auto input_checkbox" /></td>
          </tr>
        {/foreach}
        </tbody>
      </table>
      
      <div id="mass_edit">
        <select name="with_selected">
          <option value="">{lang}With Selected ...{/lang}</option>
          <option value=""></option>
          <option value="open">{lang}Mark as Open{/lang}</option>
      {if is_foreachable($milestones)}
          <option value=""></option>
        {foreach from=$milestones item=milestone}
          <option value="move_to_milestone_{$milestone->getId()}">{lang name=$milestone->getName()}Move to :name{/lang}</option>
        {/foreach}
      {/if}
          <option value=""></option>
          <option value="star">{lang}Star{/lang}</option>
          <option value="unstar">{lang}Unstar{/lang}</option>
          <option value=""></option>
          <option value="trash">{lang}Move to Trash{/lang}</option>
        </select>
        <button class="simple" type="submit" class="auto">{lang}Go{/lang}</button>
      </div>
      
      <div class="clear"></div>
      
      {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
        {if isset($active_category) && instance_of($active_category, 'Category') && $active_category->isLoaded()}
          <p class="next_page"><a href="{assemble route=project_tickets_archive project_id=$active_project->getId() page=$pagination->getNextPage() category_id=$active_category->getId()}">{lang}Next Page{/lang}</a></p>
        {else}
          <p class="next_page"><a href="{assemble route=project_tickets_archive project_id=$active_project->getId() page=$pagination->getNextPage()}">{lang}Next Page{/lang}</a></p>
        {/if}
      {/if}
    {/form}
  {else}
  {if $active_category->isLoaded()}
    <p class="empty_page">{lang}No completed tickets in this category{/lang}</p>
  {else}
    <p class="empty_page">{lang}No completed tickets in this project{/lang}</p>
  {/if}
  {/if}
  </div>
  
  <ul class="category_list">
    <li {if $active_category->isNew()}class="selected"{/if}><a href="{$tickets_archive_url}"><span>{lang}All Completed{/lang}</span></a></li>
  {if is_foreachable($categories)}
    {foreach from=$categories item=category}
    <li category_id="{$category->getId()}" {if $active_category->isLoaded() && $active_category->getId() == $category->getId()}class="selected"{/if}><a href="{assemble route=project_tickets_archive project_id=$active_project->getId() category_id=$category->getId()}"><span>{$category->getName()|clean}</span></a></li>
    {/foreach}
  {/if}
    <li id="manage_categories"><a href="{$categories_url}"><span>{lang}Manage Categories{/lang}</span></a></li>
  </ul>
  <script type="text/javascript">
    App.resources.ManageCategories.init('manage_categories');
  </script>

<div class="clear"></div>
</div>