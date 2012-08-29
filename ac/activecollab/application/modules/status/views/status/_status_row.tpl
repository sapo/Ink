{assign var=status_update_user value=$status_update->getCreatedBy()}        
<tr class="{cycle values='odd,even'}" id="status_update_{$status_update->getId()}">
  <td class="avatar"><img src="{$status_update_user->getAvatarUrl(true)}" alt="" /></td>
  <td class="message">
    {$status_update->getCreatedOn()|ago}
    <span class="author">{user_link user=$status_update_user}</span>
    <span class="update">{$status_update->getMessage()|clean|clickable}</span>
  </td>
</tr>