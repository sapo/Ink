<div class="section_container">
  <div class="col">
    {wrap field=name}
      {label for=reportName required=yes}Name{/label}
      {text_field name='report[name]' value=$report_data.name id=reportName class='required'}
    {/wrap}
  </div>
  
  <div class="col">
    {wrap field=group_name}
      {label for=reportGroupName}Report Group{/label}
      {text_field name='report[group_name]' value=$report_data.group_name id=reportGroupName}
    {/wrap}
  </div>
  
  <div class="clear"></div>
  
  {wrap field=sum_by_user}
    {label}Summarize by User{/label}
    {yes_no name='report[sum_by_user]' value=$report_data.sum_by_user}
  {/wrap}
</div>

<h2 class="section_name"><span class="section_name_span">{lang}Show Records...{/lang}</span></h2>
<div class="section_container">
  <table>
    <tr>
      <td class="report_select_label">{lang}Assigned To{/lang}</td>
      <td class="report_select_select">
        <select name="report[user_filter]" class="report_async_select">
          <option value="anybody" {if $report_data.user_filter == 'anybody'}selected="selected"{/if}>{lang}Anybody{/lang}</option>
          <option value="logged_user" {if $report_data.user_filter == 'logged_user'}selected="selected"{/if}>{lang}Person Accessing This Report{/lang}</option>
          <option value="company" class="report_async_option" {if $report_data.user_filter == 'company'}selected="selected"{/if}>{lang}Member of a Company ...{/lang}</option>
          <option value="selected" class="report_async_option" {if $report_data.user_filter == 'selected'}selected="selected"{/if}>{lang}Selected Users ...{/lang}</option>
        </select>
      </td>
      <td class="report_select_additional">
      {if $report_data.user_filter == 'company'}
        {select_company name='report[user_filter_data]' value=$report_data.user_filter_data}
      {elseif $report_data.user_filter == 'selected'}
        {select_users name='report[user_filter_data]' value=$report_data.user_filter_data}
      {/if}
      </td>
    </tr>
    <tr>
      <td class="report_select_label">{lang}For Day{/lang}</td>
      <td class="report_select_select">
        <select name="report[date_filter]" class="report_async_select">
          <option value="all" {if $report_data.date_filter == 'all'}selected="selected"{/if}>{lang}Any Time{/lang}</option>
          <option value="today" {if $report_data.date_filter == 'today'}selected="selected"{/if}>{lang}Today{/lang}</option>
          <option value="this_week" {if $report_data.date_filter == 'this_week'}selected="selected"{/if}>{lang}This Week{/lang}</option>
          <option value="last_week" {if $report_data.date_filter == 'last_week'}selected="selected"{/if}>{lang}Last Week{/lang}</option>
          <option value="this_month" {if $report_data.date_filter == 'this_month'}selected="selected"{/if}>{lang}This Month{/lang}</option>
          <option value="last_month" {if $report_data.date_filter == 'last_month'}selected="selected"{/if}>{lang}Last Month{/lang}</option>
          <option value="selected_date" class="report_async_option" {if $report_data.date_filter == 'selected_date'}selected="selected"{/if}>{lang}Specific Date ...{/lang}</option>
          <option value="selected_range" class="report_async_option" {if $report_data.date_filter == 'selected_range'}selected="selected"{/if}>{lang}Specific Range ...{/lang}</option>
        </select>
      </td>
      <td class="report_select_additional">
      {if $report_data.date_filter == 'selected_date'}
        {select_date name='report[date_from]' value=$report_data.date_from}
      {elseif $report_data.date_filter == 'selected_range'}
        <table>
	        <tr>
	          <td>{select_date name='report[date_from]' value=$report_data.date_from}</td>
	          <td style="width: 10px; text-align: center;">-</td>
	          <td>{select_date name='report[date_to]' value=$report_data.date_to}</td>
	        </tr>
	      </table>
      {/if}
      </td>
    </tr>
    
    <tr>
      <td class="report_select_label">{lang}Status{/lang}</td>
      <td class="report_select_select">
        <select name="report[billable_filter]">
          <option value="all" {if $report_data.billable_filter == 'all'}selected="selected"{/if}>{lang}Any{/lang}</option>
          <option value="all"></option>
          <option value="not_billable" {if $report_data.billable_filter == 'not_billable'}selected="selected"{/if}>{lang}Non-Billable{/lang}</option>
          <option value="billable" {if $report_data.billable_filter == 'billable'}selected="selected"{/if}>{lang}Billable{/lang}</option>
          <option value="pending_payment" {if $report_data.billable_filter == 'pending_payment'}selected="selected"{/if}>{lang}Pending Payment{/lang}</option>
          <option value="billable_not_billed" {if $report_data.billable_filter == 'billable_not_billed'}selected="selected"{/if}>{lang}Not Yet Billed (Billable or Pending Payment){/lang}</option>
          <option value="billable_billed" {if $report_data.billable_filter == 'billable_billed'}selected="selected"{/if}>{lang}Already Billed{/lang}</option>
        </select>
      </td>
      <td></td>
    </tr>
  </table>
</div>