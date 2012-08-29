<div class="col">
  {wrap field=value}
  	{label for=timeRecordValue required=yes}Hours{/label}
  	{text_field name='time[value]' value=$timetracking_data.value id=timeRecordValue class='short required'}
  	<span class="details block">{lang}Possible formats: 3:30 or 3.5{/lang}</span>
  {/wrap}
</div>

<div class="col">
  {wrap field=body}
    {label for=timeRecordBody}Summary{/label}
    {text_field name='time[body]' value=$timetracking_data.body id=timeRecordValue}
  {/wrap}
</div>

<div class="col" style="clear: left">
  {wrap field=record_date}
  	{label for=timeDate required=yes}Date{/label}
  	{select_date name='time[record_date]' value=$timetracking_data.record_date class=' required'}
  {/wrap}
</div>

<div class="col">
  {wrap field=user_id}
  	{label for=timeRecordUser required=yes}User{/label}
  {if isset($timetracking_data.record_user) && instance_of($timetracking_data.record_user, 'User')}
  	{select_user name='time[user_id]' value=$timetracking_data.user_id project=$active_project optional=no id=timeRecordUser class=required}
  {elseif isset($timetracking_data.record_user) && instance_of($timetracking_data.record_user, 'AnonymousUser')}
    <a href="mailto:{$timetracking_data.record_user->getEmail()|clean}">{$timetracking_data.record_user->getName()|clean}</a>
    <input type="hidden" name="time[user_id]" value="0" />
  {else}
    <span class="block">{lang}Unknown user{/lang}</span>
    <input type="hidden" name="time[user_id]" value="0" />
  {/if}
  {/wrap}
</div>

{wrap field=billable_status}
  {label for=timeIsBillable}Is Billable?{/label}
  {yes_no name=time[billable_status] value=$timetracking_data.billable_status id=timeIsBillable}
{/wrap}