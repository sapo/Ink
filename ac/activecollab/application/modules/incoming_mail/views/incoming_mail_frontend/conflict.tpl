{title}Resolve Conflict{/title}
{add_bread_crumb}Resolve Conflict{/add_bread_crumb}

<div id="import_dialog">
  {form action=$form_url method=post id="import_form"}
      <div class="import_dialog_left">
        {wrap field=subject}
          {label for=subject}Subject{/label}
          {text_field name=mail[subject] value=$mail_data.subject class='input_text'}
        {/wrap}
        
        {wrap field=body}
          {label for=subject}Body{/label}
          {editor_field name=mail[body] disable_image_upload=yes}{$mail_data.body}{/editor_field}
        {/wrap}
      </div>
      
      <div class="import_dialog_right">
        {wrap field=project_id}
          {label for=project_id}Create object in project:{/label}
          {select_project show_all=true user=$logged_user name="mail[project_id]" value=$mail_data.project_id id='project_id'}
        {/wrap}
        
        <div id="additional_fields_loader">
          {wrap field=created_by_id}
            {label for=created_by_id}Select new owner for this object:{/label}
            {incoming_mail_select_user name=mail[created_by_id] value=$mail_data.created_by_id id='user_id' project=$active_mail->getProject()}
          {/wrap}
          
          {wrap field=object_type}
            {label for=object_type}Object Type:{/label}
            {select_incoming_mail_object_type name=mail[object_type] value=$mail_data.object_type id='object_type' skip_comment=false}
          {/wrap}
          
          {wrap field=parent_id id='parent_id_block'}
            {label for=object_type}Parent Object:{/label}
            {select_project_object name=mail[parent_id] project=$project value=$mail_data.parent_id id='parent_id'}
          {/wrap}
          
        </div>
      </div>

          
    {wrap_buttons}
  	  {submit class="submit_button"}Submit{/submit}
    {/wrap_buttons}
    
    <script type="text/javascript">
      App.incoming_mail.importPendingEmailForm.init('import_form');
    </script>

  {/form}
</div>