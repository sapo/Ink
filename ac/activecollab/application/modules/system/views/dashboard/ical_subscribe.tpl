{title}Subscribe to iCalendar Feed{/title}
{add_bread_crumb}Subscribe{/add_bread_crumb}

<div id="ical_subscribe">
  <img src="{image_url name=calendar.gif module=system}" alt="Calendar" />
  <p>{lang}System is able to export milestone and task information so you can view them in your favorite calendar application (iCal or Outlook for example). Just subscribe to this feed:{/lang}</p>
  <p><a href="{$ical_subscribe_url|clean}">{$ical_subscribe_url|clean}</a></p>
  <p>{lang ical_url=$ical_url}If you just want to download .ics file <a href=":ical_url">click here</a>.{/lang}</p>
</div>