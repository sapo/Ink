{mobile_access_display_filter_list objects=$categories active_object=$active_category variable_name='category_id' enable_categories=$enable_categories action=$pagination_url}

<div class="wrapper">
  <div class="box">
      {if $active_category->isLoaded()}
        <ul class="menu">
          {if is_foreachable($pages)}
            {mobile_access_pages_tree pages=$pages user=$logged_user}
          {else}
            <li>{lang}No Recent Pages in this Project{/lang}</li>
          {/if}
        </ul>
      {else}
        {if is_foreachable($pages)}
          <ul class="menu">
            {foreach from=$pages item=page}
              <li class="obj_link discussions_obj_link">
                <a href="{mobile_access_get_view_url object=$page}">
                  <span class="main_line">
                    {$page->getName()|clean|excerpt:18}
                  </span>
                </a>
              </li>
            {/foreach}
          </ul>
          {mobile_access_paginator paginator=$pagination url=$pagination_url}
        {else}
          <ul class="menu">
            <li>{lang}No Recent Pages in this Project{/lang}</li>
          </ul>
        {/if}
      {/if}
  </div>
</div>