{page_object object=$active_page}
{if $active_page->getRevisionNum() > 1}
  {details}{lang version=$active_page->getRevisionNum()}Version #:version{/lang}. {action_on_by user=$active_page->getUpdatedBy() datetime=$active_page->getUpdatedOn() action='Last time updated'}{/details}
{else}
  {details}{lang version=$active_page->getRevisionNum()}Version #:version{/lang}. {action_on_by user=$active_page->getCreatedBy() datetime=$active_page->getCreatedOn() action='Created'}{/details}
{/if}
{add_bread_crumb}Latest Version{/add_bread_crumb}

{object_quick_options object=$active_page user=$logged_user}
<div class="page main_object" id="page{$active_page->getId()}">
  <div class="body">
    <dl class="properties">
      <dt>{lang}Status{/lang}</dt>
    {if $active_page->getIsArchived()}
      <dd>{lang}Archived{/lang}</dd>
    {else}
      <dd>{lang}Active{/lang}</dd>
    {/if}
  
    {if $logged_user->canSeeMilestones($active_project) && $active_page->getMilestoneId()}
      <dt>{lang}Milestone{/lang}</dt>
      <dd>{milestone_link object=$active_page}</dd>
    {/if}
    {if $active_page->hasTags()}
      <dt>{lang}Tags{/lang}</dt>
      <dd>{object_tags object=$active_page}</dd>
    {/if}
    </dl>
    
    <div class="body content">{$active_page->getFormattedBody()}</div>
  </div>
  
  <div class="resources">
    {list_subpages parent=$active_page subpages=$subpages}
    {page_versions page=$active_page versions=$versions}
    {object_tasks object=$active_page}
    {object_subscriptions object=$active_page}
    {object_attachments object=$active_page}
    
    <div class="resource object_comments" id="comments">
      <div class="body">
      {if $pagination->getLastPage() > 1}
        <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{$active_page->getViewUrl('-PAGE-')}{/pagination}</span></p>
        <div class="clear"></div>
        {/if}
        
        {if $pagination->getLastPage() > $pagination->getCurrentPage()}
          {object_comments object=$active_page comments=$comments show_header=no count_from=$count_start next_page=$active_page->getViewUrl($pagination->getNextPage())}
        {else}
          {object_comments object=$active_page comments=$comments show_header=no count_from=$count_start}
        {/if}
      </div>
    </div>
  </div>
</div>