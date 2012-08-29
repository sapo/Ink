{title}Login{/title}
<div id="login_company_logo">
  <img src="{brand what=logo}" alt="{$owner_company->getName()|clean} logo" />
</div>

<div id="auth_dialog_container">
  <div id="auth_dialog_container_inner">
    <div id="auth_dialog">
    {form method=post autofocus=$auto_focus show_errors=no}
      {wrap field=login class="auth_elements"}
        {wrap field=email}
          {label for=loginFormEmail}Email Address{/label}
          {text_field name='login[email]' value=$login_data.email id=loginFormEmail tabindex=1}
        {/wrap}
        
        {wrap field=password}
          {label for=loginFormPassword}Password{/label}
          {password_field name='login[password]' value=$login_data.password id=loginFormPassword tabindex=2}
        {/wrap}
        
        {wrap field=remember_me}
          <label for="loginFormRemember">{checkbox_field name=login[remember] checked=$login_data.remember class=inlineInput id=loginFormRemember tabindex=3} {lang}Remember me for 14 days{/lang}</label>
        {/wrap}
      {/wrap}
      
      {wrap_buttons}
        {link href="?route=forgot_password" class=forgot_password_link}Forgot password?{/link}
        {submit tabindex=4}Login{/submit}
      {/wrap_buttons}
      <div class="clear"></div>
    {/form}
    </div>
  </div>
</div>