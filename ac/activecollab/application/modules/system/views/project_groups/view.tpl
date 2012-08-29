{title}{$active_project_group->getName()}{/title}
{add_bread_crumb}Browse{/add_bread_crumb}
{add_javascript name='jquery.checkboxes'}

<div id="projectGroup">
{if is_foreachable($projects)}
  <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{$active_project_group->getViewUrl('-PAGE-')}{/pagination}</span></p>
  <div class="clear"></div>
  {foreach from=$projects item=project}
    {project_card project=$project}
  {/foreach}
  <p class="pagination bottom"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{$active_project_group->getViewUrl('-PAGE-')}{/pagination}</span></p>
  <div class="clear"></div>
{else}
  <p>{lang}There are no projects on this page{/lang}</p>
{/if}
</div>