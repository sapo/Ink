{title}Late or Scheduled for Today{/title}
{add_bread_crumb}Late / Today{/add_bread_crumb}

<div id="late_today">
{if is_foreachable($objects)}
  {if $pagination->getLastPage() > 1}
  <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=late_today page='-PAGE-'}{/pagination}</span></p>
  <div class="clear"></div>
  {/if}
  
  <table>
  {foreach from=$objects item=object}
    <tr class="{cycle values='odd,even'}">
      <td class="star">{object_star object=$object user=$logged_user}</td>
      <td class="checkbox">{object_complete object=$object user=$logged_user}</td>
      <td class="priority">{object_priority object=$object}</td>
      <td class="name">
        {$object->getVerboseType()|clean}: {object_link object=$object}
        <span class="details block">{lang}In{/lang} {project_link project=$object->getProject()} {if $object->hasAssignees()}| {object_assignees object=$object}{/if}</span>
      </td>
      <td class="due">{due object=$object}</td>
    </tr>
  {/foreach}
  </table>
  
  {if $request->isAsyncCall()}
  <p id="open_in_separate_page">{link href='?route=late_today'}Open in Separate Page{/link}</p>
  {/if}
{else}
  <p class="empty_page">{lang}There are no tasks that are late or scheduled for today{/lang}</p>
{/if}
</div>