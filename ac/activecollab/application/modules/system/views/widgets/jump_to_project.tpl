<div id="jump_to_project">
{if is_foreachable($pinned_projects)}
  <h3>{lang}Favorite Projects{/lang}</h3>
  <table>
  {foreach from=$pinned_projects key=project_id item=project_name}
    <tr class="{cycle values='odd,even' name=pinned_projects}">
      <td class="icon"><img src="{project_icon project=$project_id large=no}" alt="" /></td>
      <td class="name"><a href="{assemble route=project_overview project_id=$project_id}">{$project_name|clean}</a></td>
    </tr>
  {/foreach}
  </table>
{/if}

{if is_foreachable($active_projects)}
  {if is_foreachable($pinned_projects)}
  <h3>{lang}Other active projects{/lang}</h3>
  {/if}
  <table>
  {foreach from=$active_projects key=project_id item=project_name}
    <tr class="{cycle values='odd,even' name=active_projects}">
      <td class="icon"><img src="{project_icon project=$project_id large=no}" alt="" /></td>
      <td class="name"><a href="{assemble route=project_overview project_id=$project_id}">{$project_name|clean}</a></td>
    </tr>
  {/foreach}
  </table>
{/if}

{if !is_foreachable($pinned_projects) && !is_foreachable($active_projects)}
  <p class="empty_page">{lang}There are no active projects you are working on{/lang}</p>
{/if}
</div>