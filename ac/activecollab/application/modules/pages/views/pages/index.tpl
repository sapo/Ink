{title}Pages{/title}
{add_bread_crumb}List{/add_bread_crumb}

<div class="list_view" id="pages">
  <div class="object_list">
  {if is_foreachable($grouped_pages)}
    {if $pagination->getLastPage() > 1}
      <p class="pagination top">
        <span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=project_pages project_id=$active_project->getId() page='-PAGE-'}{/pagination}</span>
      </p>
      <div class="clear"></div>
    {/if}
  
    {foreach from=$grouped_pages key=date item=pages}
    <h3>{$date|clean}</h3>
    <table>
    {foreach from=$pages item=page}
      <tr class="{cycle values='odd,even'}">
        <td class="star">{object_star object=$page user=$logged_user}</td>
      {if $page->getRevisionNum() == 1}
        <td class="name">
          {object_link object=$page}
          <span class="block details">{lang}Initial version by{/lang} {user_link user=$page->getCreatedBy()}</span>
        </td>
        <td class="age"><span class="details">{lang}Created{/lang} {$page->getUpdatedOn()|ago}</span></td>
      {else}
        <td class="name">
          {object_link object=$page}
          <span class="block details">{lang version=$page->getRevisionNum()}Version #:version by{/lang} {user_link user=$page->getCreatedBy()}. {lang}Initial version by{/lang} {user_link user=$page->getCreatedBy()}</span>
        </td>
        <td class="age"><span class="details">{lang}Updated{/lang} {$page->getUpdatedOn()|ago}</span></td>
      {/if}
        <td class="visibility">{object_visibility object=$page user=$logged_user}</td>
      </tr>
    {/foreach}
    </table>
    {/foreach}
    
    {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
      <p class="next_page"><a href="{assemble route=project_pages project_id=$active_project->getId() page=$pagination->getNextPage()}">Next Page</a></p>
    {/if}
  {else}
    <p class="empty_page">{lang}There are no recently updated pages to show{/lang}. {if $add_page_url}{lang add_url=$add_page_url}<a href=":add_url">Create a new page now</a>{/lang}?{/if}</p>
    {empty_slate name=pages module=pages}
  {/if}
  </div>
  
  <ul class="category_list">
    <li {if $active_category->isNew()}class="selected"{/if}><a href="{$pages_url}"><span>{lang}Recently Updated{/lang}</span></a></li>
  {if is_foreachable($categories)}
    {foreach from=$categories item=category}
    <li category_id="{$category->getId()}" {if $active_category->isLoaded() && $active_category->getId() == $category->getId()}class="selected"{/if}><a href="{assemble route=project_pages project_id=$active_project->getId() category_id=$category->getId()}"><span>{$category->getName()|clean}</span></a></li>
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