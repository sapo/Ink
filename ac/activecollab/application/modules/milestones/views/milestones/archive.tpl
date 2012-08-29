{title}Completed Milestones{/title}
{add_bread_crumb}Archive{/add_bread_crumb}

<div class="list_view" id="milestones">
  <div class="object_list">
  {if is_foreachable($milestones)}
    <table>
      <tbody>
      {foreach from=$milestones item=milestone}
        <tr class="complted {cycle values='odd,even'}">
          <td class="star">{object_star object=$milestone user=$logged_user}</td>
          <td class="priority">{object_priority object=$milestone}</td>
          <td class="name">
            <a href="{$milestone->getViewUrl()}">{$milestone->getName()|clean}</a>
            <span class="details block">{action_on_by user=$milestone->getCompletedBy() datetime=$milestone->getCompletedOn() action=Completed}</span>
          </td>
          <td class="date">
          {if $milestone->isDayMilestone()}
            {$milestone->getDueOn()|date}
          {else}
            {$milestone->getStartOn()|date} &mdash; {$milestone->getDueOn()|date}
          {/if}
          </td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  {else}
    <p>{lang add_url=$add_milestone_url}There are no completed milestones in this project. <a href=":add_url">Create a milestone</a>.{/lang}</p>
  {/if}
  </div>
  
  <ul class="category_list">
    <li {if $request->getAction() != 'archive'}class="selected"{/if}><a href="{$milestones_url}"><span>{lang}Active{/lang}</span></a></li>
    <li {if $request->getAction() == 'archive'}class="selected"{/if}><a href="{assemble route=project_milestones_archive project_id=$active_project->getId()}"><span>{lang}Completed{/lang}</span></a></li>
  </ul>
  
  <div class="clear"></div>
</div>