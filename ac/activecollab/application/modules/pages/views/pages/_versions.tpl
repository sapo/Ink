{if is_foreachable($_versions)}
<div class="resource object_revisions object_section">
  <div class="head">
    <h2 class="section_name"><span class="section_name_span">
      <span class="section_name_span_span">{lang}Older Versions{/lang}</span>
      <ul class="section_options">
        <li>{link href=$_versions_page->getCompareVersionsUrl()}Compare Versions{/link}</li>
      </ul>
      <div class="clear"></div>
    </span></h2>
  </div>
  
  
  <div class="body">
    <table class="revisions_table">
      <tbody>
      {foreach from=$_versions item=_version}
        <tr class="{cycle values='odd,even'}">
          <td class="revision_num"><a href="{$_version->getViewUrl()}">#{$_version->getVersion()}</a></td>
          <td class="author">{action_on_by user=$_version->getCreatedBy() datetime=$_version->getCreatedOn()}</td>
          <td class="options">
          {if $_versions_page->canEdit($logged_user)}
            {link href=$_versions_page->getRevertUrl($_version) title='Revert to this version' confirm='Are you sure that you want to revert back to this version?' method=post}<img src="{image_url name=revert-gray.gif module=pages}" alt="" />{/link}
          {/if}
          {if $_version->canDelete($logged_user)}
            {link href=$_version->getDeleteUrl() title='Permanently delete version' class=remove_revision}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}
          {/if}
          </td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  </div>
</div>
{/if}