{flash_box}
{title revision_from=$active_commit->getRevision() revision_to=$compared->getRevision()}Comparing Revision #:revision_from with Revision #:revision_to{/title}

{page_object object=$active_commit}

{add_bread_crumb url=$active_repository->getBrowseUrl($active_revision, $active_file, $active_commit->getRevision())}{$active_file_basename|clean}{/add_bread_crumb}
{add_bread_crumb}Compare with revision {$compared->getRevision()|clean}{/add_bread_crumb}

<div id="repository_compare_files">
  <ul class="object_options">
    <li id="object_quick_option_source"><a href="{$active_repository->getBrowseUrl($active_commit, $active_file, $active_commit->getRevision())}"><span>{lang}File Source{/lang}</span></a></li>
    <li id="object_quick_option_history"><a href="{$active_repository->getFileHistoryUrl($active_commit, $active_file, $active_commit->getRevision())}"><span>{lang}File History{/lang}</span></a></li>
    <li id="object_quick_option_compare"><a href="{$active_repository->getFileCompareUrl($active_commit, $active_file, $active_commit->getRevision())}" class="current"><span>{lang}Compare{/lang}</span></a></li>
    <li id="object_quick_option_download"><a href="{$active_repository->getFileDownloadUrl($active_commit, $active_file, $active_commit->getRevision())}"><span>{lang}Download{/lang}</span></a></li>
  </ul>

  <div class="ticket main_object" id="file_">
    <div class="body">
      <dl class="properties">
        <dt>{lang}Path{/lang}</dt>
        <dd><strong>{$navigation}</strong></dd>
        <dt>{lang}Compare From{/lang}</dt>
        <dd><a href="{$latest_revision->getViewUrl()}" title="{lang}View details{/lang}" class="number">{$latest_revision->getRevision()|clean}</a></dd>
        <dt>{lang}Revision Details{/lang}</dt>
        <dd class="wrapable">{$latest_revision->getMessage()|nl2br}</dd>
        <dt>{lang}Compare To{/lang}</dt>
        <dd><a href="{$compared->getViewUrl()}">{$compared->getRevision()|clean}</a></dd>
        <dt>{lang}Revision Details{/lang}</dt>
        <dd class="wrapable">{$compared->getMessage()|nl2br}</dd>
      </dl>
    </div>
    <div class="resources">
    {if is_foreachable($diff)}
      {foreach from=$diff item=file name=file_diff}
      <table class="file_diff narrower">
        <tr>
          <td class="lines" valign="top"><pre>{$file.lines|clean}</pre></td>
          <td class="source" valign="top"><pre><table><tr><td>{$file.content}</td></tr></table></pre></td>
        </tr>
      </table>
      {/foreach}
    {else}
      <p>{lang}No diff available{/lang}</p>
    {/if}
    </div>
  </div>
</div>

<script type="text/javascript">
  App.widgets.SourceFilePages.init();
</script>