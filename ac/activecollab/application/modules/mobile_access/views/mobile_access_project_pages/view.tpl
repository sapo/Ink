<div class="wrapper">
  <div class="box">
    <div class="object_main_info">
       <h1 class="object_name">{$active_page->getName()|clean}</h1>
    </div>
    {mobile_access_object_properties object=$active_page only_show_body=true}
  </div>
  
  
  {assign var=active_page_subpages value=$active_page->getSubpages()}
  {if is_foreachable($active_page_subpages)}
  <h2 class="label">{lang}Subpages{/lang}</h2>
  <div class="box">
    <ul class="menu">
    {foreach from=$active_page_subpages item=subpage}
        <li><a href="{mobile_access_get_view_url object=$subpage}"><strong>{$subpage->getName()|clean|excerpt:22}</strong></a></li>
    {/foreach}
    </ul>
  </div>
  {/if}
  
  {assign var=active_page_revisions value=$active_page->getVersions()}
  {if is_foreachable($active_page_revisions)}
  <h2 class="label">{lang}Revisions{/lang}</h2>
  <div class="box">
    <ul class="menu">
    {foreach from=$active_page_revisions item=revision}
        <li><a href="{mobile_access_get_view_url object=$revision}">{lang}Revision{/lang}: #{$revision->getVersion()} </a></li>
    {/foreach}
    </ul>
  </div>
  {/if}

  {mobile_access_object_tasks object=$active_page}
  {mobile_access_object_comments object=$active_page user=$logged_user counter=1}
  {mobile_access_add_comment_form parent=$active_page comment_data=$comment_data}
</div>