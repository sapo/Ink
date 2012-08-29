{title}Calendar{/title}
{add_bread_crumb}{$year} / {$month}{/add_bread_crumb}

<div id="calendar">
{calendar_navigation month=$month year=$year pattern=$navigation_pattern}
{$calendar->render()}

  <p class="calendar_ical"><a href="{assemble route=ical_subscribe}">{lang}iCalendar{/lang}</a></p>
</div>