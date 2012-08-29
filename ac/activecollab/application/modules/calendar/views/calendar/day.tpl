{title}{$day|date:0}{/title}
{add_bread_crumb}{$day->getDay()}{/add_bread_crumb}

<div class="day_tasks" id="day_{$day->toMySQL()}">
{if is_foreachable($groupped_objects)}
{foreach from=$groupped_objects item=data}
  <h2 class="section_name"><span class="section_name_span">{project_link project=$data.project}</span></h2>
  <table>
  {foreach from=$data.objects item=object}
    <tr class="{cycle values='odd,even'}">
      <td class="star">{object_star object=$object user=$logged_user}</td>
      <td class="checkbox">{object_complete object=$object user=$logged_user}</td>
      <td class="priority">{object_priority object=$object}</td>
      <td class="name">
        <span class="type">{$object->getVerboseType()|clean}</span>: {object_link object=$object del_completed=no}
      </td>
    </tr>
  {/foreach}
  </table>
{/foreach}
{else}
  <p>{lang}No tasks here{/lang}</p>
{/if}
</div>