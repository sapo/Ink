{title}Mass Mailer{/title}
{add_bread_crumb}Send Message{/add_bread_crumb}

<div id="mass_mailer">
  {form action=$mass_mailer_url method=post}
  <div class="form_left_col">
    {wrap field=subject}
      {label for=emailSubject required=yes}Subject{/label}
      {text_field name=email[subject] value=$email_data.subject id=emailSubject class=title}
    {/wrap}
    
    {wrap field=body}
      {label for=emailBody required=yes}Message{/label}
      {editor_field name=email[body] id=emailBody class='validate_callback tiny_value_present' disable_image_upload=true}{$email_data.body}{/editor_field}
    {/wrap}
  </div>
  
  <div class="form_right_col">
    {wrap field=recipients}
      {label required=yes}Recipients{/label}
      {select_users name=email[recipients] value=$email_data.recipients id=recipients exclude=$exclude}
    {/wrap}
  </div>
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>