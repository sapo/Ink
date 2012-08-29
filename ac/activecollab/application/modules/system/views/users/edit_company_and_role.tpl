{title}Company and Role{/title}
{add_bread_crumb}Company and Role{/add_bread_crumb}

<div id="user_edit_company_and_role">
  {form action=$active_user->getEditCompanyAndRoleUrl() method=post}
    {wrap field=company_id}
      {label for=userCompanyId required=yes}Company{/label}
      {select_company name=user[company_id] value=$user_data.company_id optional=no id=userCompanyId class=required}
    {/wrap}
    
    {wrap field=role_id}
      {label for=userRoleId required=yes}Role{/label}
      {select_role name=user[role_id] value=$user_data.role_id active_user=$active_user optional=no id=userRoleId class=required}
    {/wrap}
    
    {wrap_buttons}
    	{submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>