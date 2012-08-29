{title not_lang=yes}{lang name=$active_user->getDisplayName()}:name's Permissions{/lang}{/title}
{add_bread_crumb}Permissions{/add_bread_crumb}

<div id="user_permissions">
  {form action=$active_project->getUserPermissionsUrl($active_user) method=post id=select_permissions}
    <p>{lang}Select project role or set custom permissions{/lang}:</p>
    {select_user_project_permissions name=project_permissions role_id=$project_user->getRoleId() permissions=$project_user->getPermissions()}
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>