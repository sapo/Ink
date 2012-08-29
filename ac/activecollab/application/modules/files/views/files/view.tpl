{page_object object=$active_file}
{add_bread_crumb}Details{/add_bread_crumb}

{object_quick_options object=$active_file user=$logged_user}
<div class="file main_object" id="file{$active_file->getId()}">
  <div class="body">
    <dl class="properties">
      <dt>{lang}File Name{/lang}</dt>
      <dd><a href="{$last_revision->getViewUrl()}">{$active_file->getName()|clean}</a></dd>
      
      <dt>{lang}Version{/lang}</dt>
      <dd>#{$active_file->getRevision()} &mdash; {action_on_by user=$last_revision->getCreatedBy() datetime=$last_revision->getCreatedOn() action=Uploaded}</dd>
      
      <dt>{lang}Size and Type{/lang}</dt>
      <dd>{$last_revision->getSize()|filesize} ({$last_revision->getMimeType()|clean})</dd>
      
    {if $active_file->getParentId()}
      <dt>{lang}Category{/lang}</dt>
      <dd>{category_link object=$active_file}</dd>
    {/if}
      
    {if $logged_user->canSeeMilestones($active_project) && $active_file->getMilestoneId()}
      <dt>{lang}Milestone{/lang}</dt>
      <dd>{milestone_link object=$active_file}</dd>
    {/if}
    {if $active_file->hasTags()}
      <dt>{lang}Tags{/lang}</dt>
      <dd>{object_tags object=$active_file}</dd>
    {/if}
    </dl>
    
  {if $last_revision->hasPreview()}
    <div class="body file_preview">
      <p><a href="{$last_revision->getViewUrl()}"><img src="{$last_revision->getPreviewUrl()}" alt="" /></a></p>
      <p class="details">{lang url=$last_revision->getViewUrl()}Image preview. <a href=":url">Click to download</a> full size image{/lang}</p>
    </div>
  {/if}
    
  {if $active_file->getBody()}
    <div class="body content">{$active_file->getFormattedBody()}</div>
  {else}
    <div class="body content details">{lang}Description for this file is not provided{/lang}</div>
  {/if}
  </div>
  
  <div class="resources">
    {object_subscriptions object=$active_file}
    {file_revisions file=$active_file}
    
    <div class="resource object_comments" id="comments">
      <div class="body">
      {if $pagination->getLastPage() > 1}
        <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{$active_file->getViewUrl('-PAGE-')}{/pagination}</span></p>
        <div class="clear"></div>
        {/if}
        
        {if $pagination->getLastPage() > $pagination->getCurrentPage()}
          {object_comments object=$active_file comments=$comments show_header=no count_from=$count_start next_page=$active_file->getViewUrl($pagination->getNextPage())}
        {else}
          {object_comments object=$active_file comments=$comments show_header=no count_from=$count_start}
        {/if}
      </div>
    </div>
  </div>
</div>