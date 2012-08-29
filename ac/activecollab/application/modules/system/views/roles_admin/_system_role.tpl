{wrap field=name}
  {label for=roleName required=yes}Name{/label}
  {text_field name=role[name] value=$role_data.name id=roleName}
{/wrap}

{wrap field=permissions}
  {select_system_permissions name=role[permissions] permissions=$permissions protect_admin_role=$protect_admin_role value=$role_data.permissions}
{/wrap}