<div class="wrapper">
  <div id="login_company_logo">
    <img src="{brand what=logo}" alt="{$owner_company->getName()|clean} logo" />
  </div>
  
  <div class="box login_box">
    {form method=post}
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
      {wrap field=use_mobile}
        <label for="loginFormRemember">{checkbox_field name=login[use_mobile] checked=$login_data.use_mobile class=inlineInput id=use_mobile tabindex=4} {lang}Use mobile interface{/lang}</label>
      {/wrap}
      
      {wrap_buttons}
        <div class="center login_submit">
          {submit tabindex=4}Login{/submit}
        </div>
      {/wrap_buttons}
    {/form}
  </div>
  
  <div class="box login_box">
        {link href="?route=mobile_access_forgot_password" class=forgot_password_link}{lang}Forgot password?{/lang}{/link}  
  </div>
</div>