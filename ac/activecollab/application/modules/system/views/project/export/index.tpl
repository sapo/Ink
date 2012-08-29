<div id="object_main_info" class="object_info">
  <h1>{lang}Project Overview{/lang}</h1>
</div>

<div id="object_details" class="object_info">
  <dl class="properties">  
    <dt>{lang}Created By{/lang}:</dt>
    <dd>{$active_project->getCreatedByName()|clean}</dd>
    
    <dt>{lang}Created On{/lang}:</dt>
    <dd>{$active_project->getCreatedOn()|datetime}</dd>
    
    <dt>{lang}Name{/lang}:</dt>
    <dd>{$active_project->getName()|clean}</dd>
    
    {if instance_of($project_leader, 'User')}
    <dt>{lang}Leader{/lang}:</dt>
    <dd>{$project_leader->getName()|clean}</dd>
    {/if}
    
    {if instance_of($project_company, 'Company')}
    <dt>{lang}Client{/lang}:</dt>
    <dd>{$project_company->getName()|clean}</dd>
    {/if}
    
    {if instance_of($project_group, 'ProjectGroup')}
    <dt>{lang}Group{/lang}:</dt>
    <dd>{$project_group->getName()|clean}</dd>
    {/if}
    
    <dt>{lang}Status{/lang}:</dt>
    <dd>{$active_project->getVerboseStatus()}</dd>
    
    <dt>{lang}Details{/lang}:</dt>
    <dd><div class="body content">{$active_project->getOverview()}</div></dd>
  </dl>
  <div class="clear"></div>
  
</div>