<div class="wrapper">
  <div class="box">
    <div class="object_main_info">
      <div class="icon">
        <img src="{$active_project->getIconUrl(true)}" alt="logo" />
      </div>
      <div class="name">
       {$active_project->getName()|clean}
      </div>
      <div class="clear"></div>
    </div>
    <dl class="object_details">
      <dt>{lang}Leader{/lang}</dt>
      <dd><a href="{mobile_access_get_view_url object=$project_leader}">{$project_leader->getName()|clean}</a></dd>
    {if instance_of($project_company, 'Company')}
      <dt>{lang}Client{/lang}</dt>
      <dd><a href="{mobile_access_get_view_url object=$project_company}">{$project_company->getName()|clean}</a></dd>
    {/if}
    {if instance_of($project_group, 'ProjectGroup')}
      <dt>{lang}Group{/lang}</dt>
      <dd><a href="{assemble route=mobile_access_projects}?group_id={$project_group->getId()}">{$project_group->getName()|clean}</a></dd>
    {/if}
      <dt>{lang}Status{/lang}</dt>
      <dd>{$active_project->getVerboseStatus()|clean}</dd>    
      <dt>{lang}Progress{/lang}</dt>
      <dd>{mobile_access_progressbar value=$active_project->getPercentsDone()}</dd>
    </dl>
    <div class="object_details">
      {$active_project->getFormattedOverview()}
    </div>
  </div>
  
  <div class="box">
    <ul class="menu">
      {foreach from=$project_sections item=project_section}
      {if $project_section.name != 'overview'}
        <li><a href="{$project_section.url}">{$project_section.full_name}</a></li>
      {/if}
      {/foreach}
    </ul>
  </div>
  
  {if is_foreachable($late_and_today)}
  <h2 class="label">{lang}Late / Today Milestones{/lang}</h2>
  <div class="box">
    <ul class="menu list">
    {foreach from=$late_and_today item=late_and_today_item}
      <li>
        <a href="{mobile_access_get_view_url object=$late_and_today_item}">
          <span class="main_link"><span>{$late_and_today_item->getName()|clean|excerpt:22}</span></span>
          <span class="details">{due object=$late_and_today_item}</span>
        </a>
      </li>
    {/foreach}
    </ul>
  </div>
  {/if}

  
  {if is_foreachable($upcoming_objects)}
  <h2 class="label">{lang}Upcoming Milestones{/lang}</h2>
  <div class="box">
    <ul class="menu list">
    {foreach from=$upcoming_objects item=upcoming_item}
      <li>
        <a href="{mobile_access_get_view_url object=$upcoming_item}">
          <span class="main_link"><span>{$upcoming_item->getName()|clean|excerpt:22}</span></span>
          <span class="details">{due object=$upcoming_item}</span>
        </a>
      </li>
    {/foreach}
    </ul>
  </div>
  {/if}
  
  {if is_foreachable($recent_activities)}
  <h2 class="label">{lang}Recent Activities{/lang}</h2>
  <div class="box">
    <ul class="menu list">
    {foreach from=$recent_activities item=recent_activity}
      <li>
        {$recent_activity->renderMobile()}
      </li>
    {/foreach}
    </ul>
  </div>
  {/if}
</div>