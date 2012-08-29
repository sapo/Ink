{title}Tags{/title}
{add_bread_crumb}All tags{/add_bread_crumb}

<div id="tags">
{if is_foreachable($tags)}
<ul>
{foreach from=$tags key=tag item=tag_details}
  <li><a href="{assemble route=project_tag project_id=$active_project->getId() tag=$tag}">{$tag|clean}</a></li>
{/foreach}
</ul>
{else}
<p>{lang}There are no tagged objects in this project{/lang}</p>
{/if}
</div>