<div id="active_projects">
{if is_foreachable($projects)}
  <table class="common_table active_projects">
    <tr>
      <th colspan="2">{lang}Projects{/lang}</th>
      <th>{lang}Client{/lang}</th>
      <th>{lang}Progress{/lang}</th>
    </tr>
  {foreach from=$projects item=project}
  {assign var=project_client value=$project->getCompany()}
    <tr class="{cycle values='odd,even'} active_project" pin_url="{$project->getPinUrl()}" unpin_url="{$project->getUnpinUrl()}" id="project_{$project->getId()}">
      <td class="icon"><a href="{$project->getOverviewUrl()}"><img src="{$project->getIconUrl()}" alt="{$project->getName()|clean}" /></a></td>
      <td class="name">{project_link project=$project}</td>
      <td class="client">
      {if instance_of($project_client, 'Company')}
        <a href="{$project_client->getViewUrl()}">{$project_client->getName()|clean}</a>
      {/if}
      </td>
      <td class="progress">{project_progress project=$project info=false}</td>
    </tr>
  {/foreach}
  </table>
  <p class="details">{lang}Drag project on Favorites Projects block to mark it as favorite{/lang}</p>
{else}
  <p class="empty_page">{lang}There are no active projects you are working on{/lang}</p>
{/if}
</div>
<script type="text/javascript">
  App.widgets.ActiveProjects.init('active_projects');
</script>
