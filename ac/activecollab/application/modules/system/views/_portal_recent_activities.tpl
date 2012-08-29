{foreach from=$recent_activities key=date item=activities name=activities}
<h3 class="day_section">{$date|clean}</h3>
<div class="day_activities">
  {foreach from=$activities item=activity name=activities}
  {assign var=activity_object value=$activity->getObject()}
  <div class="{cycle values=''} activity {$activity_object->getType()|lower}_activity {$activity->getType()|lower}_activity">
    <div class="log_icon"><img src="{$activity->getIconUrl()}" alt="" /></div>
    <div class="log_time"><span>{object_star object=$activity_object user=$logged_user}</span> &middot; {project_link project=$activity_object->getProject()} &middot; <strong>{$activity->getCreatedOn()|time}</strong></div>
    <div class="log_info">
      <div class="log_info_head">{$activity->renderHead($activity_object, false)}</div>
      {if $activity->has_body}
        {assign var=rendered_body value=$activity->renderBody($activity_object, false)}
        {if ($rendered_body)}
        <div class="log_info_body">{$rendered_body}</div>
        {/if}
      {/if}
      {if $activity->has_footer}
        {assign var=rendered_footer value=$activity->renderFooter($activity_object, false)}
        {if ($rendered_footer)}
        <div class="log_info_foot">
          {$rendered_footer}
        </div>
        {/if}
      {/if}
    </div>
  </div>
  {/foreach}
</div>
{/foreach}