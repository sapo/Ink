{title}{$day|date:0}{/title}
{add_bread_crumb}{$day->getDay()}{/add_bread_crumb}

<div class="day_tasks" id="day_{$day->toMySQL()}">
{if is_foreachable($objects)}
  <table>
  {foreach from=$objects item=object}
    <tr class="{cycle values='odd,even'}">
      <td class="star">{object_star object=$object user=$logged_user}</td>
      <td class="checkbox">{object_complete object=$object user=$logged_user}</td>
      <td class="priority">{object_priority object=$object}</td>
      <td class="name">
        {$object->getVerboseType()|clean}: {object_link object=$object del_completed=no}
      </td>
      <td class="visibility">{object_visibility object=$object user=$logged_user}</td>
    </tr>
  {/foreach}
  </table>
{else}
  <p>{lang}No tasks here{/lang}</p>
{/if}
</div>