{add_bread_crumb}Revision Details{/add_bread_crumb}
{page_object object=$active_commit}

<div id="repository_commit">
  <div class="grouped_commits">
  <table class="commit_history_table common_table">
    <tr class="commit {cycle values='odd,even'}">
      <td class="revision_details">
        <div class="commit_message">
          {$active_commit->getMessage()|nl2br}
        </div>
        <div class="commit_files">
          {foreach from=$grouped_paths item=paths key=action}
          <ul class="action_group"><li><span class="action {$path.action|clean}">{$action|source_module_get_state_string|clean}</span>
            <ul>
            {foreach from=$paths item=item}
              <li><a href="{$active_repository->getBrowseUrl($active_commit, $item, $active_commit->getRevision())}">{$item|clean}</a></li>
            {/foreach}
            </ul>
            </li>
          </ul>
          {/foreach}
        </div>
        
      </td>
    </tr>
    </table>
  </div>
  <div id="source" class="browser">
  {if is_foreachable($diff)}
    {foreach from=$diff item=file name=file_diff}
    <div class="date_slip">
      <span>{$file.file|clean}</span>
    </div>  
    <table class="file_diff" id="file_{$smarty.foreach.file_diff.iteration}">
      <tr>
        <td class="lines" valign="top"><pre>{$file.lines|clean}</pre></td>
        <td class="source" valign="top"><pre><table><tr><td>{$file.content}</tr></td></table></pre></td>
      </tr>
    </table>
    {/foreach}
  {else}
    <p>{lang}No diff available{/lang}</p>
  {/if}
  </div>
</div>