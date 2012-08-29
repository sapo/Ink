<div class="object_time_popup" id="object_time_popup_{$active_object->getId()}">
  <div class="object_time_popup_details">
    <img src="{image_url name=icon_big.gif module=timetracking}" alt="" />
    
    <dl class="details_list">
      <dt class="object_name">{$active_object->getType()|clean}</dt>
      <dd class="object_name">{object_link object=$active_object}</dd>
      
    {if $active_object->can_have_tasks}
      <dt class="object_time">{lang type=$active_object->getType()}:type Time{/lang}</dt>
      <dd class="object_time"><span class="time">{$object_time}</span> {lang}hours{/lang}</dd>
    
      <dt class="tasks_time">{lang}Tasks Time{/lang}</dt>
      <dd class="tasks_time"><span class="time">{$tasks_time}</span> {lang}hours{/lang}</dd>
    {/if}
    
      <dt class="total_time">{lang}Total{/lang}</dt>
      <dd class="total_time"><span class="time">{$total_time}</span>  {lang}hours{/lang} ({link href=$active_object->getTimeUrl()}View{/link})</dd>
    </dl>
  </div>
  
  {if $add_url}
  <p class="object_time_add_link"><a href="#">{lang}Log Time{/lang}...</a></p>
  <div class="object_time_add" style="display: none">
    {form action=$add_url method=post}
      <div class="time_popup_hours_wrapper">
        {wrap field=value}
        	{label for=timeRecordValue required=yes}Hours{/label}
        	{text_field name='time[value]' id=timeRecordValue class='short required'}
        {/wrap}
      </div>
      
      <div class="time_popup_date_wrapper">
        {wrap field=record_date}
        	{label for=timeDate required=yes}Date{/label}
        	{select_date name='time[record_date]' value=$selected_date class=required}
        {/wrap}
      </div>
      
      <div class="time_popup_user_wrapper">
        {wrap field=user_id}
        	{label for=timeRecordUser required=yes}User{/label}
        	{select_user name='time[user_id]' value=$selected_user->getId() project=$active_project optional=no id=timeRecordUser class=required}
        {/wrap}
      </div>
      
      <div class="time_popup_summary_wrapper">
        {wrap field=body}
          {label for=timeRecordBody}Summary{/label}
          {text_field name='time[body]' id=timeRecordValue}
        {/wrap}
      </div>
      
      {wrap field=billable_status}
        {label for=timeIsBillable}Is Billable?{/label}
        {yes_no name='time[billable_status]' value=$selected_billable_status id=timeIsBillable}
      {/wrap}
      
      {wrap_buttons}
        {submit accesskey=no}Log Time{/submit} {button type=button class=object_time_cancel_button}Cancel{/button}
      {/wrap_buttons}
    {/form}
  </div>
  {/if}
</div>