{title not_lang=yes}{$active_report->getName()}{/title}
{add_bread_crumb}View{/add_bread_crumb}

<div id="global_time">
  <div id="time_report">
    <table class="report dont_print">
      <tr>
        <td id="time_report_select">
          {lang}Report{/lang}: <select name="report">
          {foreach from=$grouped_reports key=group_name item=reports}
            <optgroup label="{$group_name}">
            {foreach from=$reports item=report}
            {if isset($active_project) && instance_of($active_project, 'Project')}
              <option value="{$report->getUrl($active_project)}" {if $active_report->getId() == $report->getId()}selected="selected" class="current"{/if}>{$report->getName()|clean}</option>
            {else}
              <option value="{$report->getUrl()}" {if $active_report->getId() == $report->getId()}selected="selected" class="current"{/if}>{$report->getName()|clean}</option>
            {/if}
            {/foreach}
            </optgroup>
          {/foreach}
          </select> 
        </td>
        <td id="time_report_options">
          <span class="tooltip"></span> 
        {if isset($active_project) && instance_of($active_project, 'Project')}
          <a href="{$report->getUrl($active_project)}" title="{lang}Toggle Report Details{/lang}" id="toggle_report_details"><img src="{image_url name='info-gray.gif'}" alt="" /></a> 
          <a href="{$active_report->getEditUrl($active_project)}" title="{lang}Update Report{/lang}"><img src="{image_url name=gray-edit.gif}" alt="" /></a> 
        {else}
          <a href="{$report->getUrl()}" title="{lang}Toggle Report Details{/lang}" id="toggle_report_details"><img src="{image_url name='info-gray.gif'}" alt="" /></a> 
          <a href="{$active_report->getEditUrl()}" title="{lang}Update Report{/lang}"><img src="{image_url name=gray-edit.gif}" alt="" /></a> 
        {/if}
          {link href=$active_report->getDeleteUrl() title="Delete Report" method=post confirm="Are you sure that you want to delete this report?"}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}</a>
        </td>
      </tr>
    </table>
  </div>
  
  <div id="time_report_details" class="dont_print" style="display: none">
    <p>{lang}This report displays{/lang}:</p>
    <ul>
      <li>
      {if $active_report->getUserFilter() == 'company'}
        {lang company=$active_report->getVerboseUserFilterData()}Time records assigned to members of :company company{/lang}.
      {else}
        {lang to=$active_report->getVerboseUserFilterData()}Time records assigned to :to{/lang}.
      {/if}
      </li>

      {if $active_report->getDateFilter() == 'today'}
        <li>{lang}Time records for today{/lang}.</li>
      {elseif $active_report->getDateFilter() == 'last_week'}
        <li>{lang}Time records for the last week{/lang}.</li>
      {elseif $active_report->getDateFilter() == 'this_week'}
        <li>{lang}Time records for this week{/lang}.</li>
      {elseif $active_report->getDateFilter() == 'last_month'}
        <li>{lang}Time records for the last month{/lang}.</li>
      {elseif $active_report->getDateFilter() == 'this_month'}
        <li>{lang}Time records for this month{/lang}.</li>
      {elseif $active_report->getDateFilter() == 'selected_date'}
        <li>{lang from=$active_report->getDateFrom()}Time records for :from{/lang}.</li>
      {elseif $active_report->getDateFilter() == 'selected_range'}
        <li>{lang from=$active_report->getDateFrom() to=$active_report->getDateTo()}Time records for :from &mdash; :to{/lang}.</li>
      {/if}
      
      {if $active_report->getBillableFilter() == 'billable'}
        <li>{lang}Only billable records{/lang}.</li>
      {elseif $active_report->getBillableFilter() == 'not_billable'}
        <li>{lang}Only non-billable records{/lang}.</li>
      {elseif $active_report->getBillableFilter() == 'billable_billed'}
        <li>{lang}Billable records that have been already billed{/lang}.</li>
      {elseif $active_report->getBillableFilter() == 'billable_not_billed'}
        <li>{lang}Billable records that have not yet been billed{/lang}.</li>
      {elseif $active_report->getBillableFilter() == 'pending_payment'}
        <li>{lang}Billable records that are pending payment{/lang}.</li>
      {/if}
      
      {if isset($active_project) && instance_of($active_project, 'Project')}
        <li>{lang url=$active_project->getOverviewUrl() name=$active_project->getName()}Time records in <a href=":url">:name</a> project{/lang}.</li>
      {/if}
    </ul>
    {if $active_report->getSumByUser()}
      <p>{lang}Time data will be summarized by user{/lang}.</p>
    {else}
      <p>{lang}All time records will be displayed{/lang}.</p>
    {/if}
  </div>
  
{if is_foreachable($report_records)}
{if $active_report->getSumByUser()}
  <table id="time_report_summarized_by_user">
    <thead>
      <tr>
        <th class="name">{lang}User{/lang}</th>
        <th class="hours">{lang}Hours{/lang}</th>
      </tr>
    </thead>
  {foreach from=$report_records item=report_record}
    <tr class="{cycle values='odd,even'}">
      <td class="name">{user_link user=$report_record.user}</td>
      <td class="hours">{$report_record.total_time}</td>
    </tr>
  {/foreach}
    <tfoot>
      <tr>
        <td></td>
        <td class="total">{lang}Total{/lang}: {$total_time}</td>
      </tr>
    </tfoot>
  </table>
{else}
  <table id="time_report_records">
    <thead>
      <th class="date">{lang}Date{/lang}</th>
      <th class="user">{lang}Person{/lang}</th>
      <th class="hours">{lang}Hours{/lang}</th>
      <th class="desc">{lang}Summary{/lang}</th>
      <th class="billed">{lang}Status{/lang}</th>
    {if $show_project}
      <th class="project">{lang}Project{/lang}</th>
    {/if}
    </thead>
    <tbody>
    {foreach from=$report_records item=timerecord}
      <tr class="time_record {cycle values='odd,even'} {if $timerecord->isBilled()}billed{/if}">
        <td class="date">{$timerecord->getRecordDate()|date:0}</td>
        <td class="user">{user_link user=$timerecord->getUser()}</td>
        <td class="hours">{$timerecord->getValue()}</td>
        <td class="description">
        {if instance_of($timerecord->getParent(), 'ProjectObject')}
          {object_link object=$timerecord->getParent()} 
          {if $timerecord->getBody()}
            &mdash; {$timerecord->getBody()}
          {/if}
        {else}
          {$timerecord->getBody()}
        {/if}
        </td>
      {if $timerecord->getBillableStatus() == BILLABLE_STATUS_BILLABLE}
        <td class="billed">{lang}Billable{/lang}</td>
      {elseif $timerecord->getBillableStatus() == BILLABLE_STATUS_PENDING_PAYMENT}
        <td class="billed">{lang}Pending{/lang}</td>
      {elseif $timerecord->getBillableStatus() == BILLABLE_STATUS_BILLED}
        <td class="billed">{lang}Billed{/lang}</td>
      {else}
        <td class="billed details">--</td>
      {/if}
      {if $show_project}
        <td class="project">{project_link project=$timerecord->getProject()}</td>
      {/if}
      </tr>
    {/foreach}
    </tbody>
    <tfoot>
      <tr id="records_summary">
        <td colspan="3" class="total">{lang}Total{/lang}: {$total_time}</td>
        <td colspan="3"></td>
      </tr>
    </tfoot>
  </table>
{/if}

  <p id="time_report_footer_options">
  {foreach from=$active_report->getFooterOptions($active_project, $logged_user) item=option}
    <a href="{$option.url|clean}" style="background-image: url('{$option.icon}')">{$option.text|clean}</a>
  {/foreach}
  </p>
{else}
  <p class="empty_page">{lang}This report is empty{/lang}</p>
{/if}
</div>
<script type="text/javascript">
  App.timetracking.TimeReport.init();
</script>