<ul id="project_breadcrumbs">
  {foreach from=$mobile_access_project_breadcrumbs_breadcrumbs item=breadcrumb}
  {if $breadcrumb.url}
  <li><a href="{$breadcrumb.url}">{$breadcrumb.name}</a></li>
  {else}
  <li><span>{$breadcrumb.name}</span></li>
  {/if}
  {/foreach}
</ul>

<div class="clear"></div>