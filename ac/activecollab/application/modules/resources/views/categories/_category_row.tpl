<tr category_id="{$category->getId()}" class="{cycle values='odd,even'}">
  <td class="name"><a href="{$category->getViewUrl()}">{$category->getName()|clean}</a></td>
  <td class="options">
  {if $category->canEdit($logged_user)}
    {link href=$category->getEditUrl() title='Rename' class=rename_category}<img src="{image_url name=gray-edit.gif}" alt="edit" />{/link} 
  {/if}
  {if $category->canDelete($logged_user)}
    {link href=$category->getDeleteUrl() class=move_category_to_trash}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}
  {/if}
  </td>
</tr>