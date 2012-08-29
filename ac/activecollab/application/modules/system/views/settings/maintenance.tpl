{title}Maintenance Mode{/title}
{add_bread_crumb}Settings{/add_bread_crumb}

<div id="maintenance_admin">
  {form action='?route=admin_settings_maintenance' method=post}
    {wrap field=project_id}
      {label for=maintenanceEnabled required=yes}Enable Maintenance Mode{/label}
      {yes_no name='maintenance[maintenance_enabled]' value=$maintenance_data.maintenance_enabled id=maintenanceEnabled}
    {/wrap}
    
    {wrap field=project_id}
      {label for=maintenanceMessage}Maintenance Message{/label}
      {textarea_field name='maintenance[maintenance_message]' id=maintenanceMessage}{$maintenance_data.maintenance_message}{/textarea_field}
    {/wrap}
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>

{empty_slate name=maintenance module=system}