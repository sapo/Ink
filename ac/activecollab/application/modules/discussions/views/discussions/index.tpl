{title}Discussions{/title}
{add_bread_crumb}List{/add_bread_crumb}

<div class="list_view">
  <div class="object_list">
  {if is_foreachable($discussions)}

  {if $pagination->getLastPage() > 1}
  <p class="pagination top">
    <span class="inner_pagination">
  {if isset($active_category) && instance_of($active_category, 'Category') && $active_category->isLoaded()}
    {lang}Page{/lang}: {pagination pager=$pagination}{assemble route=project_discussions project_id=$active_project->getId() category_id=$active_category->getId() page='-PAGE-'}{/pagination}
  {else}
    {lang}Page{/lang}: {pagination pager=$pagination}{assemble route=project_discussions project_id=$active_project->getId() page='-PAGE-'}{/pagination}
  {/if}
    </span>
  </p>
  <div class="clear"></div>
  {/if}
  
  <table class="discussions">
    <tr>
      <th class="icon"></th>
      <th class="name">{lang}Discussion{/lang}</th>
      <th class="comments_count">{lang}Replies{/lang}</th>
      <th class="last_comment">{lang}Last Reply{/lang}</th>
    {if $logged_user->canSeePrivate()}
      <th class="visibility"></th>
    {/if}
    </tr>
  {foreach from=$discussions item=discussion}
    <tr class="discussion {cycle values='odd,even'}" id="discussion{$discussion->getId()}">
      <td class="icon"><a href="{$discussion->getViewUrl()}" class="icon"><img src="{$discussion->getIconUrl($logged_user)}" /></td>
      <td class="name">
        <a href="{$discussion->getViewUrl()}">{$discussion->getName()|clean}</a> <span class="inline_pagination">{pagination pager=$discussion->getPagination(false, $logged_user) sensitive=true}{$discussion->getViewUrl('-PAGE-')}{/pagination}</span>
      {if $discussion->getParentId()}
        <span class="details block">{action_by user=$discussion->getCreatedBy() action=Started} {lang}in{/lang} {category_link object=$discussion}</span>
      {else}
        <span class="details block">{action_by user=$discussion->getCreatedBy() action=Started}</span>
      {/if}
      </td>
      <td class="comments_count">{$discussion->getCommentsCount()}</td>
      <td class="last_comment">
      {assign var=discussion_last_comment_by value=$discussion->getLastCommentBy()}
      {if instance_of($discussion_last_comment_by, 'User')}
        {$discussion->getLastCommentOn()|ago} {lang user_url=$discussion_last_comment_by->getViewUrl() user_name=$discussion_last_comment_by->getDisplayName(true)}by <a href=":user_url">:user_name</a>{/lang}
      {/if}
      </td>
    {if $logged_user->canSeePrivate()}
      <td class="visibility">{object_visibility object=$discussion user=$logged_user}</td>
    {/if}
    </tr>
  {/foreach}
  </table>
  <div class="clear"></div>
  
  {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
    {if isset($active_category) && instance_of($active_category, 'Category') && $active_category->isLoaded()}
      <p class="next_page"><a href="{assemble route=project_discussions project_id=$active_project->getId() category_id=$active_category->getId() page=$pagination->getNextPage()}">{lang}Next Page{/lang}</a></p>
    {else}
      <p class="next_page"><a href="{assemble route=project_discussions project_id=$active_project->getId() page=$pagination->getNextPage()}">{lang}Next Page{/lang}</a></p>
    {/if}
  {/if}
  
  {else}
    {if instance_of($active_category, 'Category') && $active_category->isLoaded()}
      <p class="empty_page">{lang}No discussions in this category{/lang}. {if $add_discussion_url}{lang add_url=$add_discussion_url}<a href=":add_url">Start one now</a>{/lang}?{/if}</p>
    {else}
      <p class="empty_page">{lang}No discussions here{/lang}. {if $add_discussion_url}{lang add_url=$add_discussion_url}<a href=":add_url">Start one now</a>{/lang}?{/if}</p>
      {empty_slate name=discussions module=discussions}
    {/if}
  {/if}
  </div>

  <ul class="category_list">
    <li {if $active_category->isNew()}class="selected"{/if}><a href="{$discussions_url}"><span>{lang}All Discussions{/lang}</span></a></li>
	{if is_foreachable($categories)}
	  {foreach from=$categories item=category}
	    <li category_id="{$category->getId()}" {if $active_category->isLoaded() && $active_category->getId() == $category->getId()}class="selected"{/if}><a href="{assemble route=project_discussions project_id=$active_project->getId() category_id=$category->getId()}"><span>{$category->getName()|clean}</span></a></li>
	  {/foreach}
	{/if}
	{if $can_manage_categories}
    <li id="manage_categories"><a href="{$categories_url}"><span>{lang}Manage Categories{/lang}</span></a></li>
  {/if}
  </ul>
  <script type="text/javascript">
    App.resources.ManageCategories.init('manage_categories');
  </script>
  
  <div class="clear"></div>
</div>