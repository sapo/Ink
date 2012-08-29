<div class="wrapper">
  <div id="login_company_logo">
    <img src="{brand what=logo}" alt="{$owner_company->getName()|clean} logo" title="{lang}Back to start page{/lang}" />
  </div>
  
  <div class="box login_box">
      {form action='?route=mobile_access_forgot_password' method=post}
        <div class="auth_elements">
          {if $success_message}
            <p>{$success_message|clean}</p>
          {/if}
          
        {if !$success_message}
          {wrap field=email}
            {label for=forgotPasswordFormEmail}Email Address{/label}
            {text_field name='forgot_password[email]' value=$forgot_password_data.email id=forgotPasswordFormEmail tabindex=1}
          {/wrap}
        {/if}
        </div>
        
        {wrap_buttons}
          <div class="center login_submit">
            {if !$success_message}
              {submit tabindex=2}Submit{/submit}
            {/if}
          </div>
        {/wrap_buttons}
        <div class="clear"></div>
      {/form}
  </div>
  
  <div class="box login_box">
    {link href='?route=mobile_access_login' class=forgot_password_link}{lang}Back to Login Form{/lang}{/link}
  </div>
</div>