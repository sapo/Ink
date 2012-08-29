{wrap field=name}
  {label for=roleName required=yes}Name{/label}
  {text_field name=role[name] value=$role_data.name id=roleName}
{/wrap}

{wrap field=permissions}
  {select_project_permissions name=role[permissions] value=$role_data.permissions}
{/wrap}