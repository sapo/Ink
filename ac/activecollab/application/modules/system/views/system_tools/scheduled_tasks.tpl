{title}Scheduled Tasks{/title}
{add_bread_crumb}Scheduled Tasks{/add_bread_crumb}

<div id="scheduled_tasks">
  <table>
    <tr>
      <th class="event">{lang}Event{/lang}</th>
      <th class="last_activity">{lang}Last Executed On{/lang}</th>
    </tr>
    
    <tr class="odd">
      <td class="event">{lang}Frequently{/lang}</td>
      <td class="last_activity">
      {if instance_of($last_frequently_activity, 'DateTimeValue')}
        {$last_frequently_activity|datetime}
      {else}
        {lang}Never executed{/lang}
      {/if}
      </td>
    </tr>
    
    <tr class="even">
      <td class="event">{lang}Hourly{/lang}</td>
      <td class="last_activity">
      {if instance_of($last_hourly_activity, 'DateTimeValue')}
        {$last_hourly_activity|datetime}
      {else}
        {lang}Never executed{/lang}
      {/if}
      </td>
    </tr>
    
    <tr class="odd">
      <td class="event">{lang}Daily{/lang}</td>
      <td class="last_activity">
      {if instance_of($last_daily_activity, 'DateTimeValue')}
        {$last_daily_activity|datetime}
      {else}
        {lang}Never executed{/lang}
      {/if}
      </td>
    </tr>
  </table>
  
  {empty_slate name=scheduled_tasks module=system}
</div>