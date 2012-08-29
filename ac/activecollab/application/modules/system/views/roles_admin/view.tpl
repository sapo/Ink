{title not_lang=true}{$active_role->getName()} {lang}Role{/lang}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

<div id="role">
{if $active_role->getType() == 'system'}
  <p>{lang role=$active_role->getName()}:role is a system role. It is used to define which sections of system users can access and use.{/lang}</p>
  {if is_foreachable($users)}
    <table class="common_table">
      <tr>
        <th class="name" colspan="2">{lang}User{/lang}</th>
        <th class="options"></th>
      </tr>
    {foreach from=$users item=user}
      <tr class="{cycle values='odd,even'}">
        <td class="icon">{link href=$user->getViewUrl()}<img src="{$user->getAvatarUrl()}" alt="avatar" />{/link}</td>
        <td class="name">{user_link user=$user}</td>
        <td class="options">
        {if $user->canEdit($logged_user)}
          {link href=$user->getEditCompanyAndRoleUrl()}<img src="{image_url name=gray-edit.gif}" alt="" />{/link} 
        {/if}
        {if $user->canDelete($logged_user)}
          {link href=$user->getDeleteUrl() confirm='Are you sure that you want to delete this user account? There is no undo!' method=post}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}
        {/if}
        </td>
      </tr>
    {/foreach}
    </table>
  {else}
    <p class="empty_page">{lang}There are no users with this role{/lang}</p>
  {/if}
{else}
  <p>{lang role=$active_role->getName()}:role is a project role. It is used to define which sections of a single project users can access and use.{/lang}</p>
  {if is_foreachable($users)}
  {foreach from=$users item=details}
  {if is_foreachable($details.users)}
  <table>
    <tr>
      <th><img src="{$details.project->getIconUrl()}" alt="" /></th>
      <th colspan="2">{project_link project=$details.project}</th>
    </tr>
    {foreach from=$details.users item=user}
    <tr class="{cycle values='odd,even'}">
      <td class="icon">{link href=$user->getViewUrl()}<img src="{$user->getAvatarUrl()}" alt="avatar" />{/link}</td>
      <td class="name">{user_link user=$user}</td>
      <td class="options">
      {if $logged_user->canChangeProjectPermissions($user, $details.project)}
        {link href=$details.project->getUserPermissionsUrl($user) title='Change Permissions'}<img src="{image_url name=gray-permissions.gif}" alt="" />{/link}
      {/if}
      
      {if $logged_user->canRemoveFromProject($user, $details.project)}
        {link href=$details.project->getRemoveUserUrl($user) method=post title='Remove from Project'}<img src="{image_url name=gray-delete.gif}" alt="" />{/link} 
      {/if}
      </td>
    </tr>
    {/foreach}
  </table>
  {/if}
  {/foreach}
  {else}
    <p class="empty_page">{lang}There are no users with this role{/lang}</p>
  {/if}
{/if}
</div>