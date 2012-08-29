{title}New System Role{/title}
{add_bread_crumb}New System Role{/add_bread_crumb}

{form action='?route=admin_roles_add_system' method=post}
  {include_template name=_system_role controller=roles_admin module=system}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}