{form action=$public_submit_url method=POST enctype="multipart/form-data" autofocus=yes ask_on_leave=yes}
  <div class="col">
      {wrap field=created_by_email}
        {label for=created_by_email required=yes}Your Email Address{/label}
        {text_field name='ticket[created_by_email]' value=$ticket_data.created_by_email id='created_by_email' class='required validate_email'}
      {/wrap}
      
      {wrap field=created_by_name}
        {label for=created_by_name required=yes}Your Name{/label}
        {text_field name='ticket[created_by_name]' value=$ticket_data.created_by_name id=created_by_name class=required}
      {/wrap}
   </div>
   
   <div class="col">
      {wrap field=parent_id}
        {label for=parent_id required=no}Category{/label}
        {select_category project=$active_project module='tickets' controller='tickets' name='ticket[parent_id]' value=$ticket_data.parent_id id=parent_id user=$logged_user}
      {/wrap}
      
      {wrap field=priority}
        {label for=priority required=no}Priority{/label}
        {select_priority name='ticket[priority]' value=$ticket_data.priority id=priority}
      {/wrap}
   </div>
   
   <div class="clear"></div>
   
   <div class="ticket_data">
      {wrap field=name}
        {label for=name required=yes}Summary{/label}
        {text_field name='ticket[name]' value=$ticket_data.name id=name  class=required}
      {/wrap}
      
      {wrap field=body}
        {label for=ticketBody required=yes}Full Description{/label}
        {editor_field name='ticket[body]' id=ticketBody class='validate_callback tiny_value_present' inline_attachments=$ticket_data.inline_attachments}{$ticket_data.body}{/editor_field}
      {/wrap}
      
      {wrap field=attachments}
        {label}Attachments{/label}
        {attach_files}
      {/wrap}
   </div>
   
   {if $captcha_enabled}
   <div>
      {wrap field=captcha class='captcha'}
        {label for=captcha required=yes}Type the code shown{/label}
        {captcha name='ticket[captcha]' value=$ticket_data.captcha id=captcha class=required captcha_url=$captcha_url}
      {/wrap}
   </div>
   {/if}
   
   {wrap_buttons}
    {submit}Submit{/submit}
   {/wrap_buttons}
{/form}