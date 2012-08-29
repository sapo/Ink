{mobile_access_display_filter_list objects=$categories active_object=$active_category variable_name='category_id' enable_categories=$enable_categories action=$pagination_url}

{if is_foreachable($discussions)}
  <ul class="list_with_icons">
  {foreach from=$discussions item=discussion}
    <li class="obj_link discussions_obj_link">
      <a href="{mobile_access_get_view_url object=$discussion}">
        <span class="main_line">
          <img src="{$discussion->getIconUrl($logged_user)}" alt="" class="icon" />
        {$discussion->getName()|clean|excerpt:18}
        </span>
      </a>
    </li>
  {/foreach}
  </ul>
  {mobile_access_paginator paginator=$pagination url=$pagination_url url_param_category_id=$selected_category_id}
{else}
  <div class="wrapper">
    <div class="box">
      <ul class="menu">
        <li>{lang}No Discussions{/lang}</li>
      </ul>
    </div>
  </div>
{/if}