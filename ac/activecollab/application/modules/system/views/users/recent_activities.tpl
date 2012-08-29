{title name=$active_user->getDisplayName()}:name's Recent Activities{/title}
{add_bread_crumb}List{/add_bread_crumb}

<div id="recent_activities">
{if is_foreachable($recent_activities)}
  {if $pagination->getLastPage() > 1}
	  <p class="pagination top">
	    <span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=people_company_user_recent_activities company_id=$active_company->getId() user_id=$active_user->getId() page='-PAGE-'}{/pagination}</span>
		</p>
  	<div class="clear"></div>
  {/if}
  
  {recent_activities recent_activities=$recent_activities}
  
  {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
  	<p class="next_page"><a href="{assemble route=people_company_user_recent_activities company_id=$active_company->getId() user_id=$active_user->getId() page=$pagination->getNextPage()}">{lang}Next Page{/lang}</a></p>
  {/if}
{else}
  <p class="empty_page">{lang}There are no recent activities logged{/lang}</p>
{/if}
</div>