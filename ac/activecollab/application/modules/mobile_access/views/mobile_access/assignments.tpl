<div class="listing_options">
  <form action="{$pagination_url}" method="GET" class="center">
    <select name="filter_id">
      {foreach from=$grouped_filters key=group_name item=filters}
        <optgroup label="{$group_name}">
          {foreach from=$filters item=filter}
            {if $active_filter->getId() == $filter->getId()}
              <option value="{$filter->getId()}" selected="selected">{$filter->getName()|clean}</option>
            {else}
              <option value="{$filter->getId()}">{$filter->getName()|clean}</option>
            {/if}
          {/foreach}
        </optgroup>
      {/foreach}
    </select>
    <button type="submit">{lang}Filter{/lang}</button>
  </form>
</div>

{if is_foreachable($objects)}
  <ul class="list_with_icons">
  {foreach from=$objects item=object}
    <li class="obj_link discussions_obj_link starred_list">
      <a href="{mobile_access_get_view_url object=$object}">
        <span class="object_type">{$object->getType()}</span>
        <span class="main_line">
        {$object->getName()|clean|excerpt:28}
        </span>
        {assign var=project value=$object->getProject()}
        <span class="project_name">{$project->getName()|clean|excerpt:35}</span>
      </a>
    </li>
  {/foreach}
  </ul>
  {mobile_access_paginator paginator=$pagination url=$pagination_url url_param_filter_id=$active_filter->getId()}
{else}
  <div class="wrapper">
    <div class="box">
      <ul class="menu">
        <li>{lang}No objects match your criteria{/lang}</li>
      </ul>
    </div>
  </div>
{/if}