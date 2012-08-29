<tr class="{cycle values='odd,even'}">
  <td class="icon"><img src="{$project->getIconUrl(true)}" alt="{$project->getName()|clean}" /></td>
  <td class="name">
    <h3><a href="{$project->getOverviewUrl()}">{$project->getName()|clean}</a></h3>
    <dl>
      <dt>{lang}Leader{/lang}</dt>
      <dd>{user_link user=$project->getLeader()}</dd>
      
    {assign var=project_company value=$project->getCompany()}
    {if instance_of($project_company, 'Company')}
      <dt>{lang}Client{/lang}</dt>
      <dd><a href="{$project_company->getViewUrl()}">{$project_company->getName()|clean}</a></dd>
    {/if}
      
    {assign var=project_group value=$project->getGroup()}
    {if instance_of($project_group, 'ProjectGroup')}
      <dt>{lang}Group{/lang}</dt>
      <dd><a href="{$project_group->getViewUrl()}">{$project_group->getName()|clean}</a></dd>
    {/if}
    </dl>
  </td>
  <td class="progress">{project_progress project=$project}</td>
  <td class="pinned">{project_pinned project=$project user=$logged_user}</td>
</tr>