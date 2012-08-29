<div id="quick_search_users_result">
  <h3>{lang}Search Results{/lang}</h3>
{if is_foreachable($results)}
  <table>
{foreach from=$results item=object}
    <tr class="{cycle values='odd,even'}">
      <td class="avatar"><img src="{$object->getAvatarUrl()|clean}" alt="{$object->getDisplayName()|clean}" /></td>
      <td class="name">{user_link user=$object}</td>
    </tr>
{/foreach}
  </table>
  {if $pagination->hasNext()}
  {assign var=items_per_page value=$pagination->getItemsPerPage()}
  <p id="quick_search_more_results"><a href="{assemble route=search q=$search_for type=$search_type}">{lang count=$pagination->getTotalItems()-$items_per_page}:count more &raquo;{/lang}</a></p>
  {/if}
{else}
  <p>{lang}We haven't found any users that matched your request{/lang}</p>
{/if}
</div>