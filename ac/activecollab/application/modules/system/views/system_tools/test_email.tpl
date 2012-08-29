{title}Test email{/title}
{add_bread_crumb}Test email{/add_bread_crumb}

{form action=$test_email_url method=post}

  {wrap field=recipient}
    {label for=emailRecipient required=yes}Recipient{/label}
    {text_field name='email[recipient]' value=$email_data.recipient id=emailRecipient class=title}
  {/wrap}
  
  {wrap field=subject}
    {label for=emailSubject required=yes}Subject{/label}
    {text_field name='email[subject]' value=$email_data.subject id=emailSubject class="title"}
  {/wrap}
  
  {wrap field=message}
    {label for=emailMessage}Body{/label}
    {textarea_field name='email[message]' id=emailMessage class=editor}{$email_data.message}{/textarea_field}
  {/wrap}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}