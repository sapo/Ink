{mobile_access_display_filter_list objects=$categories active_object=$active_category variable_name='category_id' enable_categories=$enable_categories action=$pagination_url}

{if is_foreachable($files)}
  <ul class="list_with_icons">
  {foreach from=$files item=file}
    {assign var=last_revision value=$file->getLastRevision()}
    {if instance_of($last_revision, 'Attachment')}
      <li class="obj_link discussions_obj_link no_icon">
        <a href="{mobile_access_get_view_url object=$file}">
          <span class="main_line">
          {$file->getName()|clean|excerpt:18}
          </span>
          
          <span class="details">
            {$file->getSize()|filesize}<br />{lang}Last updated by{/lang} <strong>{$last_revision->getCreatedByName()|clean}</strong><br />{$last_revision->getCreatedOn()|date}
          </span>
        </a>
      </li>
    {/if}
  {/foreach}
  </ul>
  {mobile_access_paginator paginator=$pagination url=$pagination_url url_param_category_id=$selected_category_id}
{else}
  <div class="wrapper">
    <div class="box">
      <ul class="menu">
        <li>{lang}No Files{/lang}</li>
      </ul>
    </div>
  </div>
{/if}