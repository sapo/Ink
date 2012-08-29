{title}Overview{/title}
{add_bread_crumb}Overview{/add_bread_crumb}
{page_object object=$active_project}

<div id="project_home">
  <div class="project_home_right">
  
    <div class="dashboard_sidebar alt" id="project_home_progress"><div class="dashboard_sidebar_inner"><div class="dashboard_sidebar_inner_2">
      <h2>{lang}Project Progress{/lang}</h2>
      <div id="project_progress">{project_progress project=$active_project}</div>
    </div></div></div>

    
    {if is_foreachable($late_and_today) || is_foreachable($upcoming_objects)}
    <div class="dashboard_sidebar alt"><div class="dashboard_sidebar_inner"><div class="dashboard_sidebar_inner_2">
    {if is_foreachable($late_and_today)}
      <div id="late_today">
      <h2>{lang}Late / Today Milestones{/lang}</h2>
        <table class="common_table">
          <tbody>
          {foreach from=$late_and_today item=object}
            <tr class="{if $object->isLate()}late{elseif $object->isUpcoming()}upcoming{else}today{/if}">
              <td class="info">
                <a href="{$object->getViewUrl()}">{$object->getName()|clean}</a>
              {if $object->hasAssignees(true)}
                <span class="details block">{object_assignees object=$object}</span>
              {/if}
              </td>
              <td class="due">{due object=$object}</td>
            </tr>
          {/foreach}
          </tbody>
        </table>
      </div>
    {/if}
    
    {if is_foreachable($upcoming_objects)}
      <div id="upcoming">
      <h2>{lang}Upcoming Milestones{/lang}</h2>
        <table class="common_table">
          <tbody>
          {foreach from=$upcoming_objects item=object}
            <tr class="{if $object->isLate()}late{elseif $object->isUpcoming()}upcoming{else}today{/if}">
              <td class="info">
                <a href="{$object->getViewUrl()}">{$object->getName()|clean}</a>
              {if $object->hasAssignees(true)}
                <span class="details block">{object_assignees object=$object}</span>
              {/if}
              </td>
              <td class="due">{due object=$object}</td>
            </tr>
          {/foreach}
          </tbody>
        </table>
      </div>
    {/if}
    
    </div></div></div>
    {/if}
    
    {if is_foreachable($home_sidebars)}
      {foreach from=$home_sidebars item=home_sidebar}
        {if $home_sidebar.body}
          <div class="dashboard_sidebar {if !$home_sidebar.is_important}alt{/if}" id="{$home_sidebar.id}"><div class="dashboard_sidebar_inner"><div class="dashboard_sidebar_inner_2">
            <h2>{$home_sidebar.label}</h2>
            {$home_sidebar.body}
          </div></div></div>
        {/if}
      {/foreach}
    {/if}
  </div>
    
  <div class="project_home_left">
    <div class="dashboard_wide_sidebar" id="project_details"><div class="dashboard_wide_sidebar_inner"><div class="dashboard_wide_sidebar_inner_2">
      <div class="project_details_right">
        <div id="show_me" class="{if $active_project->getOverview()}with_overview{/if}">
          {assign_var name=user_tasks_url}{assemble route=project_user_tasks project_id=$active_project->getId()}{/assign_var}
          {assign_var name=ical_subscribe_url}{assemble route=project_ical_subscribe project_id=$active_project->getId()}{/assign_var}
          {assign_var name=rss_url}{assemble route=project_rss project_id=$active_project->getId() token=$logged_user->getToken(true)}{/assign_var}
          <ul>
            <li id="show_me_assignments">{link href=$user_tasks_url}My Assignments{/link}</li>
            <li id="show_me_ical">{link href=$ical_subscribe_url}iCalendar Feed{/link}</li>
            <li id="show_me_rss">{link href=$rss_url}RSS Feed{/link}</li>
          </ul>
        </div>
      </div>
      
      <div class="project_details_left">
        {if $active_project->canEdit($logged_user)}
        <div id="project_icon"><a href="{$active_project->getEditIconUrl()}" title="{lang}Click to Change Project Icon{/lang}" class="icon_selector" id="select_project_icon"><img src="{$active_project->getIconUrl(true)}" alt="{$active_project->getName()|clean}" /></a></div>
        <script type="text/javascript">
          App.widgets.IconPicker.init('edit_icon','select_project_icon', App.lang('Change Icon'));
        </script>
        {else}
        <div id="project_icon"><img src="{$active_project->getIconUrl(true)}" alt="{$active_project->getName()|clean}" /></div>
        {/if}
        
        <div id="project_meta">
          <h2>{$active_project->getName()|clean}</h2>
          <div class="project_meta_details">
            <p>{$active_project->getFormattedOverview()}</p>
            <p>{lang}Leader{/lang}: <strong>{user_link user=$active_project->getLeader()}</strong></p>
          {if instance_of($project_company, 'Company')}
            <p>{lang}Client{/lang}: <strong><a href="{$project_company->getViewUrl()}">{$project_company->getName()|clean}</a></strong></p>
          {/if}
          {if instance_of($project_group, 'ProjectGroup')}
            <p>{lang}Group{/lang}: <strong><a href="{$project_group->getViewUrl()}">{$project_group->getName()|clean}</a></strong></p>
          {/if}
            {if $active_project->getStartsOn()}
            <p>{lang}Starts On{/lang}: <strong>{$active_project->getStartsOn()|date:0}</strong></p>
          {/if}
            
            <p>{lang}Status{/lang}: <strong>{$active_project->getVerboseStatus()|clean}</strong></p>
          </div>
        </div>
      </div>
    </div></div></div>

    <div class="dashboard_wide_sidebar alt"><div class="dashboard_wide_sidebar_inner"><div class="dashboard_wide_sidebar_inner_2">
    {if is_foreachable($grouped_activities)}
    
    <div id="recent_activities">
      {foreach from=$grouped_activities key=date item=activities name=activities}
      <h3 class="day_section">{$date|clean}</h3>
      <div class="day_activities">
        {foreach from=$activities item=activity name=activities}
        {assign var=activity_object value=$activity->getObject()}
        <div class="activity {$activity_object->getType()|lower}_activity {$activity->getType()|lower}_activity">
          <div class="log_icon"><img src="{$activity->getIconUrl()}" alt="" /></div>
          <div class="log_time">{$activity->getCreatedOn()|time}</div>
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
    
      <p class="recent_activities_rss"><a href="{assemble route=project_rss project_id=$active_project->getId() token=$logged_user->getToken(true)}">{lang}Recent Activities{/lang}</a></p>
    </div>
    {else}
      <p class="empty_page">{lang}This Project has no Recent Activities{/lang}</p>
    {/if}
    </div></div></div>
  </div>
</div>