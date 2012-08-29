{title}People{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="people">
{if is_foreachable($people)}
{foreach from=$people item=project_company}
  <div class="company">
    <h2 class="section_name"><span class="section_name_span"><a href="{$project_company.company->getViewUrl()}">{$project_company.company->getName()|clean}</a></span></h2>
    <div class="section_container">
      <table class="users">
        <tbody>
        {foreach from=$project_company.users item=user}
          <tr class="{cycle values='odd,even'}">
            <td class="avatar">
              {link href=$user->getViewUrl()}
                <img src="{$user->getAvatarUrl(false)}" alt="" />
              {/link}
            </td>
            <td class="name">
              <h3>{user_link user=$user}</h3>
            </td>
            <td class="meta">
              <dl>
                <dt>{lang}Email{/lang}</dt>
                <dd><a href="mailto:{$user->getEmail()|clean}">{$user->getEmail()|clean}</a></dd>
              {if $user->getConfigValue('im_type') && $user->getConfigValue('im_value')}
                <dt>{$user->getConfigValue('im_type')|clean}</dt>
                <dd>{$user->getConfigValue('im_value')|clean}</dd>
              {/if}
              </dl>
            </td>
            <td class="role">{$user->getVerboseProjectRole($active_project)|clean}</td>
            <td class="options">
            {if $logged_user->canChangeProjectPermissions($user, $active_project)}
              {link href=$active_project->getUserPermissionsUrl($user) title='Change Permissions'}<img src="{image_url name=gray-permissions.gif}" alt="" />{/link}
            {/if}
            
            {if $logged_user->canRemoveFromProject($user, $active_project)}
              {link href=$active_project->getRemoveUserUrl($user) method=post title='Remove from Project'}<img src="{image_url name=gray-delete.gif}" alt="" />{/link} 
            {/if}
            </td>
          </tr>
        {/foreach}
        </tbody>
      </table>
    </div>
  </div>
{/foreach}
{else}
  <p>{lang url=$active_project->getAddPeopleUrl()}<a href=":url">Click here</a> to add users to this project.{/lang}</p>
{/if}
</div>