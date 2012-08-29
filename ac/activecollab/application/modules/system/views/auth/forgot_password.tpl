{title}Forgot Password{/title}
<div id="login_company_logo">
  <img src="{brand what=logo}" alt="{$owner_company->getName()|clean} logo" title="{lang}Back to start page{/lang}" />
</div>

<div id="auth_dialog_container">
  <div id="auth_dialog_container_inner">
    <div id="auth_dialog">
    {form action='?route=forgot_password' method=post}
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
        {link href='?route=login' class=forgot_password_link}Back to Login Form{/link}
      {if !$success_message}
        {submit tabindex=2}Submit{/submit}
      {/if}
      {/wrap_buttons}
      <div class="clear"></div>
    {/form}
    </div>
  </div>
</div>