<div id="object_main_info" class="object_info">
  <h1>{lang}File{/lang}: {$object->getName()|clean}</h1>
</div>

<div id="object_details" class="object_info">
  {assign var=last_revision value=$object->getLastRevision()}
  {project_exporter_object_properties object=$object show_name=true show_file_details=true show_category=true category_url_prefix='./' show_milestone=true milestone_url_prefix='../milestones/' show_tags=true show_details=true show_milestone_link=$exporting_milestones attachments_url_prefix='../uploaded_files/'}
</div>

<div id="object_revisions" class="object_info">
{if instance_of($last_revision, 'Attachment')}
  <h3>{lang}Latest Version{/lang}</h3>
  <table class="common_table">
    <tr>
      <td class="column_id">#{$object->countRevisions()-1}</td>
      <td class="column_name"><a href="../uploaded_files/{$last_revision->getId()}_{$last_revision->getName()|clean}">{$last_revision->getName()|clean}</a></td>
      <td class="column_author">{$last_revision->getCreatedByName()|clean}</td>
      <td class="column_date">{$last_revision->getCreatedOn()|date}</td>
      <td class="column_options"><a href="../uploaded_files/{$last_revision->getId()}_{$last_revision->getName()|clean}">{lang}Download{/lang}</a></td>
    </tr>
  </table>
  {assign var=revisions value=$object->getRevisions()}
  {if $object->countRevisions() > 1}
  <h3 style="padding-top: 10px">{lang}Older Versions{/lang}</h3>
  <table class="common_table">
  {counter start=$object->countRevisions()-2 direction=down name=revision_num assign=revision_num}
  {foreach from=$revisions  item=revision}
    {if $revision->getId() != $last_revision->getId()}
    <tr>
      <td class="column_id">#{$revision_num}{counter name=revision_num}</td>
      <td class="column_name"><a href="../uploaded_files/{$revision->getId()}_{$revision->getName()|clean}">{$revision->getName()|clean}</a></td>
      <td class="column_author">{$revision->getCreatedByName()|clean}</td>
      <td class="column_date">{$revision->getCreatedOn()|date}</td>
      <td class="column_options"><a href="../uploaded_files/{$revision->getId()}_{$revision->getName()|clean}">{lang}Download{/lang}</a></td>
    </tr>
    {/if}
  {/foreach}
  </table>
  {/if}
{else}
  <p>{lang}This has no revisions{/lang}</p>
{/if}
</div>

{project_exporter_comments comments=$comments attachments_url_prefix='../uploaded_files/'}