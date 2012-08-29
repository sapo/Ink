{title}Update Password{/title}
{add_bread_crumb}Update Password{/add_bread_crumb}

<div id="user_edit_password">
  {form action=$active_user->getEditPasswordUrl() method=post}
    {wrap field=password}
      {label for=userPassword required=yes}Password{/label}
      {password_field name=user[password] id=userPassword class='required'}
    {/wrap}
    
    {wrap field=repeat_password}
      {label for=userRepeatPassword required=yes}Repeat password{/label}
      {password_field name=user[repeat_password] id=userRepeatPassword class="required validate_same_as userPassword"}
    {/wrap}
    
    {wrap_buttons}
    	{submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>