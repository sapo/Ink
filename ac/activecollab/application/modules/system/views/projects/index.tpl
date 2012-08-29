{title}Active Projects{/title}
{add_bread_crumb}Active Projects{/add_bread_crumb}

<div id="projects" class="list_view">
  <div class="object_list">
    {if $logged_user->isOwner()}
    <p class="pagination top" id="group_projects_by">
      <span class="inner_pagination">
      {if $group_by == 'client'}
        {lang}Client{/lang} 
      {else}
        <a href="{assemble route=projects group_by=client}">{lang}Client{/lang}</a> 
      {/if}
        | 
      {if $group_by == 'group'}
        {lang}Group{/lang} 
      {else}
        <a href="{assemble route=projects group_by=group}">{lang}Group{/lang}</a> 
      {/if}
      </span>
    </p>
    {/if}
  {if $group_by == 'client' && instance_of($selected_company, 'Company')}
    <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=projects page='-PAGE-' company_id=$selected_company->getId() group_by=$group_by}{/pagination}</span></p>
  {elseif $group_by == 'group' && instance_of($selected_group, 'ProjectGroup')}
    <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=projects page='-PAGE-' group_id=$selected_group->getId() group_by=$group_by}{/pagination}</span></p>
  {else}
    <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=projects page='-PAGE-' group_by=$group_by}{/pagination}</span></p>
  {/if}
    
    <div class="clear"></div>
    
  {if is_foreachable($projects)}
    <table class="projects_table">
    {foreach from=$projects item=project}
      <tr class="project_row {cycle values='odd,even'}">
      {project_card project=$project}
      </tr>
    {/foreach}
    </table>
    
    {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
      {if $group_by == 'client' && instance_of($selected_company, 'Company')}
        <p class="next_page"><a href="{assemble route=projects page=$pagination->getNextPage() company_id=$selected_company->getId() group_by=$group_by}">{lang}Next Page{/lang}</a></p>
      {elseif $group_by == 'group' && instance_of($selected_group, 'ProjectGroup')}
        <p class="next_page"><a href="{assemble route=projects page=$pagination->getNextPage() group_id=$selected_group->getId() group_by=$group_by}">{lang}Next Page{/lang}</a></p>
      {else}
        <p class="next_page"><a href="{assemble route=projects page=$pagination->getNextPage() group_by=$group_by}">{lang}Next Page{/lang}</a></p>
      {/if}
    {/if}
    
  {else}
    {if instance_of($selected_group, 'ProjectGroup')}
    <p class="empty_page">{lang}There are no active projects in this group{/lang}</p>
    {else}
    <p class="empty_page">{lang}There are no active projects{/lang}</p>
    {/if}
  {/if}
  
  {if $group_by == 'client'}
    {if instance_of($selected_company, 'Company')}
      <p class="archive_link"><a href="{assemble route=projects_archive group_by=client company_id=$selected_company->getId()}">{lang}Archive{/lang}</a></p>
    {else}
      <p class="archive_link">{link href='?route=projects_archive&group_by=client'}Archive{/link}</p>
    {/if}
  {else}
    {if instance_of($selected_group, 'ProjectGroup')}
      <p class="archive_link"><a href="{assemble route=projects_archive group_by=group group_id=$selected_group->getId()}">{lang}Archive{/lang}</a></p>
    {else}
      <p class="archive_link">{link href='?route=projects_archive&group_by=group'}Archive{/link}</p>
    {/if}
  {/if}
  </div>

{if $group_by == 'client'}
  <ul class="category_list">
    <li {if $selected_company->isOwner()}class="selected"{/if}><a href="{assemble route=projects group_by=client}"><span>{lang}Internal Projects{/lang}</span></a></li>
{if is_foreachable($companies)}
  {foreach from=$companies item=company}
    <li {if instance_of($selected_company, 'Company') && $selected_company->getId() == $company->getId()}class="selected"{/if}><a href="{assemble route=projects company_id=$company->getId() group_by=client}"><span>{$company->getName()|clean}</span></a></li>
  {/foreach}
{/if}
  </ul>
{else}
  <ul class="category_list project_group_list">
    <li {if !instance_of($selected_group, 'ProjectGroup')}class="selected"{/if}><a href="{assemble route=projects group_by=group}"><span>{lang}All Active Projects{/lang}</span></a></li>
{if is_foreachable($groups)}
  {foreach from=$groups item=group}
    <li project_group_id="{$group->getId()}" {if instance_of($selected_group, 'ProjectGroup') && $selected_group->getId() == $group->getId()}class="selected"{/if}><a href="{assemble route=projects group_id=$group->getId() group_by=group}"><span>{$group->getName()|clean}</span></a></li>
  {/foreach}
  {if $logged_user->isProjectManager() || $logged_user->isAdministrator()}
    <li id="manage_project_groups"><a href="{assemble route=project_groups}"><span>{lang}Manage Groups{/lang}</span></a></li>
  {/if}
{/if}
  </ul>
  <script type="text/javascript">
    App.system.ManageProjectGroups.init('manage_project_groups');
  </script>
{/if}
  
  <div class="clear"></div>
</div>