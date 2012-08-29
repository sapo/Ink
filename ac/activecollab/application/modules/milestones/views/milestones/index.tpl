{title}Active milestones{/title}
{add_bread_crumb}Active{/add_bread_crumb}

<div class="list_view" id="milestones">
  <div class="object_list">
  {if is_foreachable($milestones)}
    <table>
      <tbody>
      {foreach from=$milestones item=milestone}
        <tr class="{if $milestone->isLate()}late{elseif $milestone->isUpcoming()}upcoming{else}today{/if} {cycle values='odd,even'}">
          <td class="star">{object_star object=$milestone user=$logged_user}</td>
          <td class="priority">{object_priority object=$milestone}</td>
          <td class="name">
            <a href="{$milestone->getViewUrl()}">{$milestone->getName()|clean}</a>
            {if $milestone->hasAssignees(true)}
            <span class="details block">{object_assignees object=$milestone}</span>
            {/if}
          </td>
          <td class="date">
          {if $milestone->isDayMilestone()}
            {$milestone->getDueOn()|date:0}
          {else}
            {$milestone->getStartOn()|date:0} &mdash; {$milestone->getDueOn()|date:0}
          {/if}
          </td>
          <td class="due">{due object=$milestone}</td>
        </tr>
      {/foreach}
      </tbody>
    </table>
    <p class="milestones_ical"><a href="{assemble route=project_ical_subscribe project_id=$active_project->getId()}">{lang}iCalendar{/lang}</a></p>
  {else}
    <p class="empty_page">{lang}No active milestones here{/lang}. {lang add_url=$add_milestone_url}Would you like to <a href=":add_url">create one</a>{/lang}?</p>
    {empty_slate name=milestones module=milestones}
  {/if}
  </div>
  
  <ul class="category_list">
    <li {if $request->getAction() != 'archive'}class="selected"{/if}><a href="{$milestones_url}"><span>{lang}Active{/lang}</span></a></li>
    <li {if $request->getAction() == 'archive'}class="selected"{/if}><a href="{assemble route=project_milestones_archive project_id=$active_project->getId()}"><span>{lang}Completed{/lang}</span></a></li>
  </ul>
  
  <div class="clear"></div>
</div>