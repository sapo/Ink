{title}File History{/title}

{page_object object=$latest_revision}

{add_bread_crumb url=$active_repository->getBrowseUrl($active_revision, $active_file, $active_commit->getRevision())}{$active_file_basename|clean}{/add_bread_crumb}
{add_bread_crumb}View File History{/add_bread_crumb}

<div id="repository_file_history">
  <ul class="object_options">
    <li id="object_quick_option_source"><a href="{$active_repository->getBrowseUrl($active_commit, $active_file, $active_commit->getRevision())}"><span>{lang}File Source{/lang}</span></a></li>
    <li id="object_quick_option_history"><a href="{$active_repository->getFileHistoryUrl($active_commit, $active_file, $active_commit->getRevision())}" class="current"><span>{lang}File History{/lang}</span></a></li>
    <li id="object_quick_option_compare"><a href="{$active_repository->getFileCompareUrl($active_commit, $active_file, $active_commit->getRevision())}"><span>{lang}Compare{/lang}</span></a></li>
    <li id="object_quick_option_download"><a href="{$active_repository->getFileDownloadUrl($active_commit, $active_file, $active_commit->getRevision())}"><span>{lang}Download{/lang}</span></a></li>
  </ul>

  <div class="ticket main_object" id="file_">
    <div class="body">
      <dl class="properties">
        <dt>{lang}Path{/lang}</dt>
        <dd><strong>{$navigation}</strong></dd>
      </dl>
    </div>
    <div class="resources">
      <div class="source_container">
      {if is_foreachable($commits)}
        <div class="grouped_commits">
          <table class="commit_history_table common_table">
            <tr>
              <th>{lang}Revision{/lang}</th>
              <th>{lang}Comment{/lang}</th>
              <th class="revision_date">{lang}Date{/lang}</th>
              <th>{lang}Author{/lang}</th>
            </tr>
            {foreach from=$commits name=commit_list item=commit}
            <tr class="commit {cycle values='odd,even'}">
              <td class="revision_number">
                <a href="{$commit->getViewUrl()}" title="{lang}View details{/lang}" class="number">{$commit->getRevision()|clean}</a><br />
              </td>
              <td class="revision_details">
                <div class="commit_message">
                  {$commit->getMessage()|nl2br}
                </div>
              </td>
              <td class="revision_date">
                {$commit->getCreatedOn()|date}
              </td>
              <td class="revision_user">
                {$commit->getAuthor($active_repository)}
              </td>
            </tr>
            {/foreach}
          </table>
        </div>
      {else}
        <p>{lang}There is no version history for this file{/lang}</p>
      {/if}
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  App.widgets.SourceFilePages.init();
</script>