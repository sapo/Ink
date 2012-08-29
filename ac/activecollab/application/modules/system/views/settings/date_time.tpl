{title}Date and Time Settings{/title}
{add_bread_crumb}Date and Time Settings{/add_bread_crumb}

{form action='?route=admin_settings_date_time' method=post}
  <div class="section_container">
    <div class="col">
      {wrap field=time_timezone}
        {label for=timeTimezone}Timezone{/label}
        {select_timezone name='date_time[time_timezone]' value=$date_time_data.time_timezone optional=yes id=timeTimezone}
      {/wrap}
    </div>
    
    <div class="col">
      {wrap field=time_dst}
        {label for=timeDST}Daylight saving time{/label}
        {yes_no name=date_time[time_dst] value=$date_time_data.time_dst id=timeDST}
      {/wrap}
    </div>
    
    <div class="clear"></div>
    
    {wrap field=first_week_day}
      {label for=timeFirstWeekDay}First day in week{/label}
      {select_week_day name=date_time[time_first_week_day] value=$date_time_data.time_first_week_day id=timeFirstWeekDay}
    {/wrap}
  </div>
  
  <h2 class="section_name"><span class="section_name_span">{lang}Formatting{/lang}</span></h2>
  <div class="section_container">
    <div class="col">
      {wrap field=format_date}
        {label for=localeFormatDate}Date Format{/label}
        {select_datetime_format name=date_time[format_date] value=$date_time_data.format_date optional=no id=localeFormatDate mode=date}
      {/wrap}
    </div>
    
    <div class="col">
      {wrap field=format_time}
        {label for=localeFormatTime}Time Format{/label}
        {select_datetime_format name=date_time[format_time] value=$date_time_data.format_time optional=no id=localeFormatTime mode=time}
      {/wrap}
    </div>
  </div>
  
  {wrap_buttons}
	  {submit}Submit{/submit}
  {/wrap_buttons}
{/form}