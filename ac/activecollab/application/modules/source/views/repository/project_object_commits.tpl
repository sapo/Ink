{title}Related commits{/title}
{details}
  <a href="{$active_object->getViewUrl()}">{lang object_type=$active_object->getType() object_name=$active_object->getName()}for :object_type :object_name{/lang}</a>
{/details}

{add_bread_crumb}List of commits{/add_bread_crumb}

<div id="repository_project_object_commits">
  {if is_foreachable($commits) and $commits|@count > 0}
  <div class="grouped_commits">
  {foreach from=$commits item=commits_day key=date}
    <div class="date_slip">
      <span>{$date}</span>
    </div>
    <table class="commit_history_table common_table">
    {foreach from=$commits_day name=commit_list item=commit}
      <tr class="commit {cycle values='odd,even'}">
      <td class="revision_number">
        <a href="{$commit->getViewUrl()}" title="{lang}View commit details{/lang}" class="number">{$commit->getRevision()|clean}</a><br />
      </td>
      <td class="revision_user">
        {$commit->getAuthor($active_repository)}
      </td>
      <td class="revision_details">
        <div class="commit_message">
          {$commit->getMessage()|nl2br}
        </div> 
      </td>
    </tr>
    {/foreach}
  </table>
  {/foreach}
  </div>
{else}
  <p class="empty_page">{lang}There are no commits related to this project object in the database.{/lang}</p>
{/if}
</div>