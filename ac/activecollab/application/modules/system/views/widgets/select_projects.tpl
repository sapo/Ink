<div id="{$widget_id}_popup" class="select_projects_widget_popup">
{if is_foreachable($projects)}
  <table>
  {foreach from=$projects key=project_id item=project_name}
    <tr class="{cycle values='odd,even' name=active_projects}">
      <td class="checkbox"><input type="checkbox" value="{$project_id}" class="auto input_checkbox" {if in_array($project_id, $selected_project_ids)}checked="checked"{/if} /></td>
      <td class="icon"><img src="{project_icon project=$project_id large=no}" alt="" /></td>
      <td class="name"><a href="{assemble route=project_overview project_id=$project_id}">{$project_name|clean}</a></td>
    </tr>
  {/foreach}
  </table>
{else}
  <p class="empty_page">{lang}There are no projects to select from{/lang}</p>
{/if}
</div>