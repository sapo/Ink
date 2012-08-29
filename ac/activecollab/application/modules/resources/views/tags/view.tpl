{title tag=$tag}Tag: :tag{/title}
{add_bread_crumb}{$tag}{/add_bread_crumb}

<div id="view_tag">
{if is_foreachable($objects)}
  {if $pagination->getLastPage() > 1}
    <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{$tag_url_pattern}{/pagination}</span></p>
    <div class="clear"></div>
  {/if}
  
  {list_objects objects=$objects show_header=no show_checkboxes=no}
{else}
  <p class="empty_page">{lang tag=$tag}There are no objects tagged with :tag{/lang}</p>
{/if}
</div>