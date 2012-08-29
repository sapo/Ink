{title}People{/title}
{add_bread_crumb}Companies{/add_bread_crumb}

<div id="companies" class="list_view">
  <div class="object_list">
{if is_foreachable($companies)}
  {if $pagination->getLastPage() > 1}
    <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=people_archive page='-PAGE-'}{/pagination}</span></p>
    <div class="clear"></div>
  {/if}
  
    <table>
    {foreach from=$companies item=company}
      <tr class="{cycle values='odd,even'}">
        <td class="icon"><img src="{$company->getLogoUrl()}" alt="" /></td>
        <td class="name">{company_link company=$company}
        {if $company->isOwner()}
          <span class="details">({lang}Owner Company{/lang})</span>
        {/if}
        </td>
      </tr>
    {/foreach}
    </table>
    
  {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
    <p class="next_page"><a href="{assemble route=people_archive page=$pagination->getNextPage()}">{lang}Next Page{/lang}</a></p>
  {/if}
{else}
    <p class="empty_page">{lang}There are no companies in the archive{/lang}</p>
{/if}
  </div>
  
  <ul class="category_list">
    <li><a href="{assemble route=people}"><span>{lang}Companies{/lang}</span></a></li>
  {if $logged_user->isPeopleManager()}
    <li class="selected"><a href="{assemble route=people_archive}"><span>{lang}Archive{/lang}</span></a></li>
  {/if}
  </ul>
</div>