{title}My Project Tasks{/title}
{add_bread_crumb}List My Tasks{/add_bread_crumb}

<div id="assignments">
  <div id="assignments_list">
  {if is_foreachable($assignments)}
    {if $pagination && ($pagination->getLastPage() > 1)}
    <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=project_user_tasks project_id=$active_project->getId() page=-PAGE-}{/pagination}</span></p>
    {/if}
    <div class="clear"></div>
    
    <table class="assignments">
      <tr>
        <th class="star"></th>
        <th class="checkbox"></th>
        <th class="priority"></th>
        <th class="name">{lang}Name{/lang}</th>
        <th class="project">{lang}Project{/lang}</th>
        <th class="option"></th>
      </tr>
    {foreach from=$assignments item=assignment}
      <tr class="assignment_row {cycle values='odd,even'}">
        <td class="star">{object_star object=$assignment user=$logged_user}</td>
        <td class="checkbox">{link href=$assignment->getCompleteUrl(true) class=complete_assignment}<img src="{image_url name=icons/not-checked.gif}" alt="toggle" />{/link}</td>
        <td class="priority">{object_priority object=$assignment}</td>
        <td class="name">
          {$assignment->getVerboseType()|clean}: {object_link object=$assignment}
          <span class="details block">{object_assignees object=$assignment}{if $assignment->getDueOn()} | {due object=$assignment}.{/if}</span>
        </td>
        <td class="project">{project_link project=$assignment->getProject()}</td>
        <td class="options">
        {object_subscription object=$assignment user=$logged_user} 
        {if module_loaded('timetracking') && timetracking_can_add_for($logged_user, $assignment)}
          {object_time object=$assignment show_time=no} 
        {/if}
        {if $assignment->canEdit($logged_user)}
          {link href=$assignment->getEditUrl() title='Edit...'}<img src='{image_url name=gray-edit.gif}' alt='' />{/link} 
        {/if}
        {if $assignment->canDelete($logged_user)}
          {link href=$assignment->getTrashUrl() title='Move to Trash' class=remove_assignment}<img src='{image_url name=gray-delete.gif}' alt='' />{/link}
        {/if}
        </td>
      </tr>
    {/foreach}
    </table>
  {else}
    <p class="empty_page">{lang}There are no tasks assigned to you in this project{/lang}</p>
  {/if}
  </div>
</div>