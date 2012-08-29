<div class="wrapper">
  <div class="box">
    <div class="object_main_info">
       <h1 class="object_name">{$version->getName()|clean}</h1>
    </div>
      <div class="object_details">
        {$version->getFormattedBody()}      
      </div>
  </div>
  
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
</div>