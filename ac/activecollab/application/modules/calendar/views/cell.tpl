<td class="day_cell {if $day->isToday(get_user_gmt_offset())}today{/if} {if $day->isWeekend()}weekend{else}weekday{/if} {if is_foreachable($day_data)}not_empty_day{else}empty_day{/if}" id="day-{$day->getYear()}-{$day->getMonth()}-{$day->getDay()}">
  <div class="inner">
    <div class="day_num"><a href="{$day_url}">{$day->getDay()}</a></div>
    <div class="day_brief">
    {if is_foreachable($day_data)}
      <ul>
      {foreach from=$day_data item=object}
        <li>{$object->getVerboseType()|clean}: {object_link object=$object}</li>
      {/foreach}
      </ul>
    {/if}
    </div>
    <div class="day_details"></div>
  </div>
</td>