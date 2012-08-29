{if is_foreachable($_activities)}
  {foreach from=$_activities key=date item=activities}
  <h3 class="day_section">{$date|clean}</h3>
  <table>
    <tbody>
    {foreach from=$activities item=activity}
      {assign var=object value=$activity->getObject()}
      {if instance_of($object, 'ProjectObject')}
        <tr class="{cycle values='odd,even'} {$object->getType()|lower|clean}_activity">
          <td class="star">{object_star object=$object user=$logged_user}</td>
          {if $activity->getAction() == 'Completed'}
            <td class="name completed"><strong>{$object->getVerboseType()|clean}</strong>: {object_link object=$object}
          {else}
            <td class="name"><strong>{lang}{$object->getVerboseType()|clean}{/lang}</strong>: {object_link object=$object del_completed=no}
          {/if}
          {if $activity->getComment()}
            <span class="details block">{$activity->getComment()|clean}</span>
          {/if}
          </td>
          {if $_activities_project_column}
          <td class="project">{lang}In{/lang} {project_link project=$activity->getProject()}</td>
          {/if}
          <td class="user">{action_by user=$activity->getCreatedBy() action=$activity->getAction() short_names=yes}</td>
        {if $logged_user->canSeePrivate()}
          <td class="visibility">{object_visibility object=$object user=$logged_user}</td>
        {/if}
        </tr>
      {/if}
    {/foreach}
    </tbody>
  </table>
  {/foreach}
{/if}