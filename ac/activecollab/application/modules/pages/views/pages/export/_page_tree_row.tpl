{if $_page}
  <tr class="{cycle values='odd,even'}">
    <td class="column_id"><a href="{$_list_objects_url_prefix}{$_page->getType()|strtolower}_{$_page->getId()}.html">{$_page->getId()}</a></td>
    <td class="column_name"><a href="{$_list_objects_url_prefix}{$_page->getType()|strtolower}_{$_page->getId()}.html">{$_indent} {$_page->getName()|clean}</a></td>
    {if $_page->getRevisionNum() == 1}
      <td class="version details">v1</td>
      <td class="column_date">{$_page->getCreatedOn()|date}</td>       
      <td class="column_author">{project_exporter_user_name user=$_page->getCreatedBy()}</td>
    {else}
      <td class="version details">v{$_page->getRevisionNum()}</td>
      <td class="column_date">{$_page->getUpdatedOn()|date}</td>       
      <td class="column_author">{project_exporter_user_name user=$_page->getUpdatedBy()}</td>
    {/if}
  </tr>
{/if}