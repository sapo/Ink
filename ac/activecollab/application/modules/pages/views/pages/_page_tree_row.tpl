{if $_page}
<tr class="{cycle values='odd,even'}">
  <td class="star">{object_star object=$_page user=$logged_user}</td>
{if $_page->getRevisionNum() == 1}
  <td class="name"><span class="indent">{$_indent}</span>{if $_page->getIsArchived()}<del>{object_link object=$_page}</del>{else}{object_link object=$_page}{/if}</td>
  <td class="version details">v1</td>
  <td class="age details">{$_page->getCreatedOn()|ago} {lang}by{/lang} {user_link user=$_page->getCreatedBy() short=yes}</td>
{else}
  <td class="name"><span class="indent">{$_indent}</span>{if $_page->getIsArchived()}<del>{object_link object=$_page}</del>{else}{object_link object=$_page}{/if}</td>
  <td class="version details">v{$_page->getRevisionNum()}</td>
  <td class="age details">{$_page->getUpdatedOn()|ago} {lang}by{/lang} {user_link user=$_page->getUpdatedBy() short=yes}</td>
{/if}
{if $_show_visibility}
  <td class="visibility">{object_visibility object=$_page user=$logged_user}</td>
{/if}
</tr>
{/if}