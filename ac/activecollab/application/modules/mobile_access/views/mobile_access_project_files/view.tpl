<div class="wrapper">
  <div class="box">
    <div class="object_main_info">
       <h1 class="object_name">{$active_file->getName()|clean}</h1>
    </div>
    {mobile_access_object_properties object=$active_file show_name=true show_file_details=true show_tags=true show_body=true show_milestone=true show_category=true}
    <div class="download_details">
      <a href="{$last_revision->getViewUrl()}">{lang}Download{/lang}</a>
    </div>
  </div>
  
  {if is_foreachable($revisions) && count($revisions)>1}
    <h2 class="label">{lang}Older Versions{/lang}</h2>
    <div class="box">
      <ul class="menu list">
        {foreach from=$revisions item=revision}
        {if $revision->getId()!=$last_revision->getId()}
          <li>
            <a href="{$revision->getViewUrl()}">
              <span class="main_link"><span>{$revision->getName()|clean|excerpt:27}</span></span>
              <span class="details"><strong>{$revision->getCreatedByName()|clean}</strong><br />{$revision->getCreatedOn()|date}</span>
            </a>
          </li>
        {/if}
        {/foreach}
      </ul>
    </div>
  {/if}

  {mobile_access_object_comments object=$active_file user=$logged_user counter=1}
  {mobile_access_add_comment_form parent=$active_file comment_data=$comment_data}
</div>