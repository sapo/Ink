{title}Completed Checklists{/title}
{add_bread_crumb}Archive{/add_bread_crumb}

<div id="checklists">
  {if is_foreachable($checklists)}
    <table>
      <tbody>
      {foreach from=$checklists item=checklist}
        <tr class="{cycle values='odd,even'}">
          <td class="star">{object_star object=$checklist user=$logged_user}</td>
          <td class="name">{object_link object=$checklist}</td>
          <td class="status details right">{lang total=$checklist->countTasks() open=$checklist->countOpenTasks()}:open open tasks of :total tasks in the list{/lang}</td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  {else}
    <p class="empty_page">{lang}There are no active checklists here{/lang}. {if $add_checklist_url}{lang add_url=$add_checklist_url}Would you like to <a href=":add_url">create one</a>{/lang}?{/if}</p>
  {/if}
</div>


