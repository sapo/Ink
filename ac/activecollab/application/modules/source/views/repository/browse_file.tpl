{title}File Source{/title}

{page_object object=$latest_revision}

{add_bread_crumb url=$active_repository->getBrowseUrl($active_revision, $active_file, $active_commit->getRevision())}{$active_file_basename|clean}{/add_bread_crumb}
{add_bread_crumb}View Source{/add_bread_crumb}

<div id="repository_file">
  <ul class="object_options">
    <li id="object_quick_option_source"><a href="{$active_repository->getBrowseUrl($active_commit, $active_file, $active_commit->getRevision())}" class="current"><span>{lang}File Source{/lang}</span></a></li>
    <li id="object_quick_option_history"><a href="{$active_repository->getFileHistoryUrl($active_commit, $active_file, $active_commit->getRevision())}"><span>{lang}File History{/lang}</span></a></li>
    <li id="object_quick_option_compare"><a href="{$active_repository->getFileCompareUrl($active_commit, $active_file, $active_commit->getRevision())}"><span>{lang}Compare{/lang}</span></a></li>
    <li id="object_quick_option_download"><a href="{$active_repository->getFileDownloadUrl($active_commit, $active_file, $active_commit->getRevision())}"><span>{lang}Download{/lang}</span></a></li>
  </ul>

  <div class="ticket main_object" id="file_">
    <div class="body">
      <dl class="properties">
        <dt>{lang}Path{/lang}</dt>
        <dd><strong>{$navigation}</strong></dd>
        <dt>{lang}Revision Details{/lang}</dt>
        <dd class="wrapable">
        {if $latest_revision->getMessage()}
          {$latest_revision->getMessage()|nl2br}
        {else}
          {lang}Commit message was not provided{/lang}
        {/if}
        </dd>
      </dl>
    </div>
    <div class="resources">
      <table class="file_source">
        <tr>
          <td class="lines">
            <pre>{$lines|clean}</pre>
          </td>
          <td class="source">
          {if $source}
            <pre class="source">{$source|clean}</pre>
          {else}
            {lang}Not a text file{/lang}
          {/if}
          </td>
        </tr>
      </table>
    </div>
  </div>
</div>