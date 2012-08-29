<div id="object_main_info" class="object_info">
  <h1>{lang}Page{/lang}: {$page->getName()|clean}</h1>
</div>

<div id="object_details" class="object_info">
  {project_exporter_object_properties object=$page show_category=true category_url_prefix='./' show_milestone=true milestone_url_prefix='../milestones/' show_milestone_link=$exporting_milestones attachments_url_prefix='../uploaded_files/'}
  <div class="body">
  {$page->getFormattedBody()}
  </div>
</div>

<div id="object_subobjects" class="object_info">
  <h3>{lang}Subpages{/lang}</h3>
  {project_exporter_list_objects objects=$subpages url_prefix='./'}
</div>

{project_exporter_object_tasks object=$page}

{assign var=revisions value=$page->getVersions()}
{if is_foreachable($revisions)}
<div id="object_revisions" class="object_info">
  <h3>{lang}Revisions{/lang}</h3>
  <table class="common_table">
  {foreach from=$revisions item=revision}
    <tr>
      <td class="column_id"><a href="./revision_{$revision->getPageId()}_{$revision->getVersion()}.html">#{$revision->getVersion()}</a></td>
      <td class="column_date">{$revision->getCreatedOn()|date}</td>
      <td class="column_name">{$revision->getCreatedByName()|clean}</td>
    </tr>
  {/foreach}
  </table>
</div>
{/if}

{project_exporter_comments comments=$comments attachments_url_prefix='../uploaded_files/'}