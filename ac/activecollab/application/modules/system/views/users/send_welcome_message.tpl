{title}Send Welcome Message{/title}
{add_bread_crumb}Send Welcome Message{/add_bread_crumb}

<div id="send_welcome_message" {if $request->isAsyncCall()}class="async"{/if}>
  <p>{lang}Welcome message includes information user needs in order to log in: <strong>link to login form, email and password</strong>. Optionally, you can personalize message or provide more information using Personalize Message field below{/lang}.</p>
  <p>{lang}For security reasons system does not store passwords in readable format. Because of this, <strong>random password will be generated</strong> each time you send a welcome message{/lang}!</p>
  {form action=$active_user->getSendWelcomeMessageUrl() method=post}
    {wrap field=message}
      {label for=sendWelcomeMessageMessage}Personalize Message{/label}
      {textarea_field name='welcome_message[message]' id=sendWelcomeMessageMessage}{$welcome_message_data.message}{/textarea_field}
    {/wrap}
    <p class="details boxless">{lang}HTML not supported! Line breaks are preserved. Links are recognized and converted{/lang}.</p>
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>
<script type="text/javascript">
  App.system.SendWelcomeMessage.init('send_welcome_message');
</script>