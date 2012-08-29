{title}New Project Role{/title}
{add_bread_crumb}New Project Role{/add_bread_crumb}

{form action='?route=admin_roles_add_project' method=post}
  {include_template name=_project_role controller=roles_admin module=system}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}