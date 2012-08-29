{page_object object=$active_repository}
{add_bread_crumb}Commit history{/add_bread_crumb}

<div id="repository_history">
  {if $filter_by_author}
    <p>{lang clean_params=false created_by=$filter_by_author.user_object history_url=$active_repository->getHistoryUrl()}You are viewing commits created by <b>:created_by</b> only. Click <a href=":history_url">here</a> to view all commits made to this repository{/lang}</p>
    {assign var=pagination_filter value=$filter_by_author.user_name}
  {else}
    {assign var=pagination_filter value=null}
  {/if}
  
  {if $commits|@count > 0}
  <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=repository_history page='-PAGE-' filter_by_author=$pagination_filter project_id=$project->getId() repository_id=$active_repository->getId()}{/pagination}</span></p>
  {/if}

  {if is_foreachable($commits) and $commits|@count > 0}
  <div class="history_header">
    <a href="#" id="toggle_all_paths" class="show button"><span></span></a>
  </div>
  
  <div class="grouped_commits">
  {foreach from=$commits item=commits_day key=date}
    <div class="date_slip">
      <span>{$date}</span>
    </div>
    <table class="commit_history_table common_table">
      {foreach from=$commits_day name=commit_list item=commit}
      <tr class="commit {cycle values='odd,even'}">
        <td class="revision_number">
          <a href="{$commit->getViewUrl()}" title="{lang}View details{/lang}" class="number">{$commit->getRevision()|clean}</a><br />
        </td>
        <td class="revision_user">
        {assign var=commit_author value=$commit->getCreatedBy()}
        <a href="{$active_repository->getHistoryUrl($commit->getCreatedByName())}">{$commit_author->getDisplayName(true)}</a>
        </td>
        <td class="revision_details">
          <div class="commit_message">
            {$commit->getMessage()|nl2br}
          </div>
          <div class="commit_files">
          {foreach from=$commit->getPaths() item=path name=files_list key=action}
            <ul class="action_group">
              <li><span class="action {$action}">{lang}{$action|source_module_get_state_string|clean}{/lang}</span>
                <ul>
                {foreach from=$path item=item}
                  <li><a href="{$active_repository->getBrowseUrl($commit, $item)}">{$item|clean}</a></li>
                {/foreach}
                </ul>
              </li>
            </ul>
            {/foreach}
          </div>
        </td>
        <td class="revision_files">
          <a href="#" title="{lang}Toggle affected paths list{/lang}" class="toggle_files commit_modified_files">{lang paths_count=$commit->total_paths}:paths_count files{/lang}</a>
        </td>
      </tr>
      {/foreach}
    </table>
  {/foreach}
  </div>
{else}
  <p class="empty_page">{lang update_url=$active_repository->getUpdateUrl()}There are no commits in the database. Would you like to <a href=":update_url" class="repository_ajax_update">update</a> this repository{/lang}?</p>
{/if}  
{if $commits|@count > 0}
  <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=repository_history page='-PAGE-' project_id=$project->getId() filter_by_author=$pagination_filter repository_id=$active_repository->getId()}{/pagination}</span></p>
  <div class="clear"></div>
{/if}
  <div class="resources">{object_subscriptions object=$active_repository}</div>
</div>