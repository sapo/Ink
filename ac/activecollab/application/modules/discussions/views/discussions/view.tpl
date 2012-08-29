{title}{$active_discussion->getName()}{/title}
{page_object object=$active_discussion}
{add_bread_crumb}{lang page=$pagination->getCurrentPage()}Page :page{/lang}{/add_bread_crumb}

{object_quick_options object=$active_discussion user=$logged_user}
<div class="discussion main_object" id="discussion{$active_discussion->getId()}">
  <div class="body">
    <dl class="properties">
    {if $logged_user->canSeeMilestones($active_project) && $active_discussion->getMilestoneId()}
      <dt>{lang}Milestone{/lang}</dt>
      <dd>{milestone_link object=$active_discussion}</dd>
    {/if}
    
    {if $active_discussion->getParent()}
      <dt>{lang}Category{/lang}</dt>
      <dd>{category_link object=$active_discussion}</dd>
    {/if}
    
      <dt>{lang}Subscribers{/lang}</dt>
      <dd>{object_subscriptions object=$active_discussion brief=yes}</dd>
    
    {if $active_discussion->hasTags()}
      <dt>{lang}Tags{/lang}</dt>
      <dd>{object_tags object=$active_discussion}</dd>
    {/if}
    </dl>
    
    <div class="body content discussion_details_toggled" id="discussions_body_{$active_discussion->getId()}">{$active_discussion->getFormattedBody()}</div>
    {if $active_discussion->getSource() == $smarty.const.OBJECT_SOURCE_EMAIL}
      <script type="text/javascript">
        App.EmailObject.init('discussions_body_{$active_discussion->getId()}');
      </script>
    {/if}
  </div>
  
  <div class="resources">
    <div class="discussion_details_toggled">
      {object_attachments object=$active_discussion brief=yes}
    </div>
    {if $pagination->getCurrentPage() != 1}
      <script type="text/javascript">
        $('.discussion_details_toggled').hide();
      </script>
    {/if}
    
    <div class="resource object_comments" id="comments">
      <div class="body">
      {if $pagination->getLastPage() > 1}
        <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{$active_discussion->getViewUrl('-PAGE-')}{/pagination}</span></p>
        <div class="clear"></div>
        {/if}
        
        {if $pagination->getLastPage() > $pagination->getCurrentPage()}
          {object_comments object=$active_discussion comments=$comments show_header=no count_from=$count_start next_page=$active_discussion->getViewUrl($pagination->getNextPage())}
        {else}
          {object_comments object=$active_discussion comments=$comments show_header=no count_from=$count_start}
        {/if}
      </div>
    </div>
  </div>
</div>