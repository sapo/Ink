{title}{$active_category->getName()|clean}{/title}
{add_bread_crumb}View{/add_bread_crumb}

<div class="category" id="category={$active_category->getId()}">
{if is_foreachable($category_objects)}
  <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{$active_category->getViewUrl('-PAGE-')}{/pagination}</span></p>
<div class="clear"></div>
{list_objects objects=$category_objects}
  <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{$active_category->getViewUrl('-PAGE-')}{/pagination}</span></p>
<div class="clear"></div>
{else}
  <p>{lang categories_url=$categories_url}There are no object on this page. Go back to <a href=":categories_url">categories page</a>.{/lang}</p>
{/if}
</div>