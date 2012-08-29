{if is_foreachable($_file_revisions) && count($_file_revisions) > 1}
<div class="resource file_revisions object_section">
  <div class="head">
    <h2 class="section_name comments_section_name"><span class="section_name_span">{lang}Older Versions{/lang}</span></h2>
  </div>
  <div class="body">
    <table id="file_versions">
    {assign_var name=black_hole}{counter name=file_revisions start=$_file_revisions_count direction=down}{/assign_var}
    {foreach from=$_file_revisions item=_file_revision name=file_revisions}
    {if $smarty.foreach.file_revisions.iteration > 1}
      <tr class="{cycle values='odd,even'}">
        <td class="num">#{counter name=file_revisions}</td>
        <td class="thumbnail"><a href="{$_file_revision->getViewUrl()}"><img src="{$_file_revision->getThumbnailUrl()}" alt="{$_file_revision->getName()|clean}" /></a></td>
        <td class="name">
          <dl class="details_list">
            <dt>{lang}Name{/lang}</dt>
            <dd>{link href=$_file_revision->getViewUrl()}{$_file_revision->getName()|clean}{/link} </dd>
            
            <dt>{lang}Size and Type{/lang}</dt>
            <dd class="light">{$_file_revision->getSize()|filesize}, {$_file_revision->getMimeType()}</dd>
            
            <dt></dt>
            <dd class="light">{action_on_by action="Uploaded" user=$_file_revision->getCreatedBy() datetime=$_file_revision->getCreatedOn()}</dd>
          </dl>
        </td>
        <td class="options">{if $_file_revision->canDelete($logged_user)}<a href={$_file_revision->getDeleteUrl()} title="{lang}Delete Permanently{/lang}"><img src="{image_url name=gray-delete.gif}" alt="" /></a>{/if}</td>
      </tr>
    {/if}
    {/foreach}
    </table>
  </div>
</div>
{/if}