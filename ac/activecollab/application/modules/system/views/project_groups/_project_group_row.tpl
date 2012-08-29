<tr project_group_id="{$project_group->getId()}" class="{cycle values='odd,even'}">
  <td class="name"><a href="{$project_group->getViewUrl()}">{$project_group->getName()|clean}</a></td>
  <td class="options">
  {if $project_group->canEdit($logged_user)}
    {link href=$project_group->getEditUrl() title='Rename' class=rename_project_group}<img src="{image_url name=gray-edit.gif}" alt="edit" />{/link} 
  {/if}
  {if $project_group->canDelete($logged_user)}
    {link href=$project_group->getDeleteUrl() class=delete_project_group}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}
  {/if}
  </td>
</tr>