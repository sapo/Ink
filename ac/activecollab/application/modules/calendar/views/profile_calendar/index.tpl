{title}Calendar{/title}
{add_bread_crumb}{$year} / {$month}{/add_bread_crumb}

<div id="calendar">
{calendar_navigation month=$month year=$year pattern=$navigation_pattern}
{$calendar->render()}

{if $logged_user->isProjectManager() || ($logged_user->getId() == $active_user->getId())}
  <p class="calendar_ical"><a href="{assemble route=profile_calendar_ical_subscribe company_id=$active_user->getCompanyId() user_id=$active_user->getId()}">{lang}iCalendar{/lang}</a></p>
{/if}
</div>