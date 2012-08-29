{title}Repository Users{/title}
{details}
  {lang}Map repository users with activeCollab people added to this project{/lang}.
{/details}

{add_bread_crumb url=$active_repository->getBrowseUrl($active_revision, $active_file)}{$active_file_basename|clean}{/add_bread_crumb}
{add_bread_crumb}Repository Users{/add_bread_crumb}

<div id="repository_users">
  {form class=map_user_form action=$repository_user_add_url method=post}
    <table id="new_record">
      <tr>
        <td>
          <select id="repository_user" name="repository_user">
          {foreach from=$repository_users item=repository_user}
            <option value="{$repository_user}">{$repository_user}</option>
          {/foreach}
          </select>
        </td>
        <td>{select_user id=user_id name=user_id project=$active_project}</td>
        <td class="actions">{submit id='submit_map'}Submit{/submit}</td>    
      </tr>
    </table>
  {/form}

  <p id="all_mapped">{lang}All repository users are mapped with activeCollab users{/lang}.</p>
  <p id="no_users">{lang}There are no commits in the repository and repository users can't be mapped yet{/lang}.</p>

  <table id="records" class="common_table mapped_users">
    <thead>
      <tr>
        <th>{lang}Repository User{/lang}</th>
        <th>{lang}activeCollab User{/lang}</th>
        <th>{lang}Options{/lang}</th>
      </tr>
    </thead>
    <tbody>
    {if is_foreachable($source_users)}
    {foreach from=$source_users item=source_user name=source_foreach}
      <tr class="mapped_users_list">
        <td>{$source_user->getRepositoryUser()|clean}</td>
        <td>{user_link user=$source_user->system_user}</td>
        <td class="options">{link href=$source_user->getDeleteUrl($active_project) title='Remove this mapping' name=$source_user->getRepositoryUser() class=remove_source_user}<img src='{image_url name=gray-delete.gif}' alt='' />{/link}</td>
      </tr>
    {/foreach}
    {/if}
    </tbody>
  </table>
</div>

{if is_foreachable($source_users) and !is_foreachable($repository_users)}
<script type="text/javascript">
  $('#all_mapped').show();
  $('#new_record').hide();
  $('#no_users').hide();
</script>
{/if}

{if !is_foreachable($source_users) and !is_foreachable($repository_users)}
<script type="text/javascript">
  $('#all_mapped').hide();
  $('#new_record').hide();
  $('#no_users').show();
  $('#records').hide();
</script>
{/if}

{if is_foreachable($source_users) and is_foreachable($repository_users)}
<script type="text/javascript">
  $('#all_mapped').hide();
  $('#new_record').show();
  $('#no_users').hide();
</script>
{/if}

{if !is_foreachable($source_users) and is_foreachable($repository_users)}
<script type="text/javascript">
  $('#all_mapped').hide();
  $('#no_users').hide();
  $('#new_record').show();
  $('#records').hide();
</script>
{/if}