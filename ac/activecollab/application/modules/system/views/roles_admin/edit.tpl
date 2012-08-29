{title}Edit Role{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

<div id="edit_role">
  {form action=$active_role->getEditUrl() method=post}
    {if $active_role->getType() == 'project'}
      {include_template name=_project_role controller=roles_admin module=system}
    {else}
      {include_template name=_system_role controller=roles_admin module=system}
    {/if}
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>