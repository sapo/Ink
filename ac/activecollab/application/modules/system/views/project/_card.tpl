<td class="icon"><img src="{$_card_project->getIconUrl(true)}" alt="{$_card_project->getName()|clean}" /></td>
<td class="name">
  <h3><a href="{$_card_project->getOverviewUrl()}">{$_card_project->getName()|clean}</a></h3>
  <dl>
    <dt>{lang}Leader{/lang}</dt>
    <dd>{user_link user=$_card_project->getLeader()}</dd>
  {if instance_of($_card_project_company, 'Company')}
    <dt>{lang}Client{/lang}</dt>
    <dd><a href="{$_card_project_company->getViewUrl()}">{$_card_project_company->getName()|clean}</a></dd>
  {/if}
  {if $_card_project->getStartsOn()}
    <dt>{lang}Starts On{/lang}</dt>
    <dd>{$_card_project->getStartsOn()|date:0}</dd>
  {/if}
  </dl>
</td>
<td class="progress">
  {project_progress project=$_card_project}
</td>