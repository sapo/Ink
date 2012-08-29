<div class="form_wrapper">
  {if isset($active_time_record) && $active_time_record->isLoaded()}
    {assign_var name=project_time_url}{assemble route=project_time project_id=$active_project->getId()}{/assign_var}
    <p class="flash" id="success"><span class="flash_inner">{lang name=$active_project->getName() url=$project_time_url}Time record has been created in <a href=":url">:name</a> project{/lang}</span></p>
  {/if}
  
  {form method=post id=quick_add_time_record action=$quick_add_url}
    <div id="wrap_hours_date">
      {wrap field=value class=left_record_field}
        {label for=quick_add_time_record_value required=yes}Hours{/label}
        {text_field name='time_record[value]' value=$time_record_data.value id=quick_add_time_record_value class=required}
      {/wrap}
      
      {wrap field=record_date class=right_record_field}
        {label for=quick_add_time_record_date required=yes}Date{/label}
        {select_date name='time_record[record_date]' value=$time_record_data.record_date show_timezone=no id=quick_add_time_record_date class=required}
      {/wrap}
    </div>
    
    <div id="wrap_billable_summary">
      {wrap field=value class=left_record_field}
        {label for=quick_add_time_record_billable_status}Is Billable{/label}
        {yes_no name='time_record[billable_status]' value=$time_record_data.billable_status id=quick_add_time_record_billable_status}
      {/wrap}
      
      {wrap field=body class=right_record_field}
        {label for=quick_add_time_record_summary}Summary{/label}
        {text_field name='time_record[body]' value=$time_record_data.body id=quick_add_time_record_summary}
      {/wrap}
    </div>
    
    <div class="wizardbar">
      {submit accesskey=no class='submit'}Submit{/submit}<a href="#" class="wizzard_back">{lang}Back{/lang}</a>
    </div>
    <input type="hidden" name="time_record[project_id]" value="{if $project_id}{$project_id}{else}{$time_record_data.project_id}{/if}" />
    <input type="hidden" name="time_record[user_id]" value="{$logged_user->getId()}" />
  {/form}
</div>
<script type="text/javascript">
{literal}
  if (App.ModalDialog.isOpen) {
    App.ModalDialog.setWidth(500);
  } // if
{/literal}
</script>