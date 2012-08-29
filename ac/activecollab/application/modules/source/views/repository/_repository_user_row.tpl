<tr class="mapped_users_list">
  <td>{$source_user->getRepositoryUser()|clean}</td>
  <td>{user_link user=$source_user->system_user}</td>
  <td class="options">{link href=$source_user->getDeleteUrl($active_project) title='Remove this mapping' name=$source_user->getRepositoryUser() class=remove_source_user}<img src='{image_url name=gray-delete.gif}' alt='' />{/link}</td>
</tr>