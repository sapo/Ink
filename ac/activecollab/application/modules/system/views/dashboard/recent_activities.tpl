{if $request->getController() == 'dashboard' && $request->getAction() == 'recent_activities'}
  {title}Recent Activities{/title}
  {add_bread_crumb}List{/add_bread_crumb}
{/if}

<div id="recent_activities">
{if is_foreachable($grouped_activities)}
  {foreach from=$grouped_activities key=date item=activities name=activities}
  <h3 class="day_section">{$date|clean}</h3>
  <div class="day_activities">
    {foreach from=$activities item=activity name=activities}
    {assign var=activity_object value=$activity->getObject()}
    <div class="{cycle values=''} activity {$activity_object->getType()|lower}_activity {$activity->getType()|lower}_activity">
      <div class="log_icon"><img src="{$activity->getIconUrl()}" alt="" /></div>
      <div class="log_time"><span>{object_star object=$activity_object user=$logged_user}</span> &middot; {project_link project=$activity_object->getProject()} &middot; <strong>{$activity->getCreatedOn()|time}</strong></div>
      <div class="log_info">
        <div class="log_info_head">{$activity->renderHead($activity_object, true)}</div>
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

  <p class="recent_activities_rss"><a href="{assemble route=rss token=$logged_user->getToken(true)}">{lang}Recent Activities{/lang}</a></p>
{else}
  <p class="empty_page">{lang}There are no activities logged{/lang}</p>
{/if}
</div>