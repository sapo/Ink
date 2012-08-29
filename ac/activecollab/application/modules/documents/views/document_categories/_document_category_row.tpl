<tr document_category_id="{$document_category->getId()}" class="{cycle values='odd,even'}">
  <td class="name"><a href="{$document_category->getViewUrl()}">{$document_category->getName()|clean}</a></td>
  <td class="options">
  {if $document_category->canEdit($logged_user)}
    {link href=$document_category->getEditUrl() title='Rename' class=rename_document_category}<img src="{image_url name=gray-edit.gif}" alt="edit" />{/link} 
  {/if}
  {if $document_category->canDelete($logged_user)}
    {link href=$document_category->getDeleteUrl() class=delete_document_category}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}
  {/if}
  </td>
</tr>