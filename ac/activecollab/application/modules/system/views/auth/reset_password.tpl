{title}Reset Password{/title}
<div id="login_company_logo">
  <img src="{brand what=logo}" alt="{$owner_company->getName()|clean} logo" />
</div>

<div id="auth_dialog_container">
  <div id="auth_dialog_container_inner">
    <div id="auth_dialog">
    {form method=post autofocus=$auto_focus show_errors=no}
    <p>{lang name=$user->getDisplayName()}Use the form below to reset password for :name's account{/lang}:</p>
      {wrap field=passwords class="auth_elements"}
        {wrap field=password}
          {label for=resetFormPassword}New Password{/label}
          {password_field name='reset[password]' value=$reset_data.password id=resetFormPasswordA tabindex=1}
        {/wrap}
        
        {wrap field=password_a}
          {label for=resetFormPasswordA}Repeat{/label}
          {password_field name='reset[password_a]' value=$reset_data.password_a id=resetFormPasswordA tabindex=2}
        {/wrap}
      {/wrap}
      
      {wrap_buttons}
        {link href='?route=login' class=forgot_password_link}Back to Login Form{/link}
        {submit tabindex=4}Reset{/submit}
      {/wrap_buttons}
      <div class="clear"></div>
    {/form}
    </div>
  </div>
</div>