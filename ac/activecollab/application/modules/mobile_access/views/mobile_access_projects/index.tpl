<div class="listing_options">
  <form action="{$paginator_url}" method="GET" class="center">
    <select name="group_id">
      <option value="">{lang}Any Groups{/lang}</option>
      {if is_foreachable($groups)}
      {foreach from=$groups item=group}
        {if $selected_group_id == $group->getId()}
        <option value="{$group->getId()}" selected="selected">{$group->getName()|clean}</option>
        {else}
        <option value="{$group->getId()}">{$group->getName()|clean}</option>
        {/if}
      {/foreach}
      {/if}
    </select>
    <button type="submit">{lang}Filter{/lang}</button>
  </form>

</div>

{if is_foreachable($projects)}
  <ul class="list_with_icons">
  {foreach from=$projects item=project}
    <li class="obj_link">
      <a href="{mobile_access_get_view_url object=$project}">
        <span class="main_line">
          <img src="{$project->getIconUrl(true)}" alt="logo" class="icon" />
          {$project->getName()|clean|excerpt:18}
        </span>
      </a>
    </li>
  {/foreach}
  </ul>
  {mobile_access_paginator paginator=$pagination url=$paginator_url url_param_group_id=$selected_group_id}
{else}
  <div class="wrapper">
    <div class="box">
      <ul class="menu">
        <li>{lang}No Projects{/lang}</li>
      </ul>
    </div>
  </div>
{/if}