{title}Source Version Control{/title}
{add_bread_crumb}{lang}Repositories{/lang}{/add_bread_crumb}

  <div id="repository_index" class="repository_listing">
  {if is_foreachable($repositories)}
    <table>
      <tr>
        <th></th>
        <th>{lang}Repository Name{/lang}</th>
        <th class="revision">{lang}Head{/lang}</th>
        <th class="last_commit">{lang}Last Commit{/lang}</th>
        <th class="graph">{lang}Activity Graph{/lang}</th>
        <th></th>
      </tr>
    {foreach from=$repositories item=repository}
      <tr class="{cycle values='odd,even'}">
        <td class="star">{object_star object=$repository user=$logged_user}</td>
        <td class="name">
          <strong>{object_link object=$repository}</strong>
          <span class="block details">
             <a href="{$repository->getUrl()|clean}">{$repository->getUrl()|clean}</a>
           </span>
        </td>
        <td class="revision">
          {if !instance_of($repository->last_commit, 'Commit')}
            -
          {else}
            <a href="{$repository->last_commit->getViewUrl()}">{$repository->last_commit->getRevision()|clean}</a>
          {/if}
        </td>
        <td class="last_commit">
          {if !instance_of($repository->last_commit, 'Commit')}
            -
          {else}
            {$repository->last_commit->getAuthor()}<br />
            {$repository->last_commit->getCreatedOn()|date}
          {/if}          
        </td>
        <td class="graph">
        {assign var=activity value=$repository->getRecentActivity()}
        {if is_foreachable($activity)}
          <ul class="timeline">
          {foreach from=$activity item=item}
            <li>
              <a href="#" title="{lang commits=$item.commits day=$item.created_on}:commits commits on :day{/lang}" onclick="return false;">
                <span class="count" style="height:{$item.percentage}%"></span>
              </a>
            </li>
          {/foreach} 
          </ul>
        {/if}
        </td>
        <td class="star">{object_subscription object=$repository user=$logged_user}</td>
        <td class="visibility">{object_visibility object=$repository user=$logged_user}</td>
      </tr>
    {/foreach}
    </table>
  {else}
    <p class="empty_page">{lang add_url=$add_repository_url}There are no repositories added. Would you like to <a href=":add_url">create one</a>{/lang}?</p>
  {/if}
  </div>