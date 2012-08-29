<div id="object_main_info" class="object_info">
  <h1>{lang}Timerecords{/lang}: {$current_month.month_string}, {$current_month.year}</h1>
</div>

{if is_foreachable($distinct_months)}
<div id="object_details">
  <dl class="properties">
    <dt>{lang}Month{/lang}:</dt>
    <dd>
      <ul class="category_list">
          <li><a href="./index.html">{lang}All Months{/lang},</a></li>
      {foreach from=$distinct_months item=distinct_month}
        {if $current_month==$distinct_month}
          <li class="selected"><a href="./monthly_{$distinct_month.month}_{$distinct_month.year}.html">{$distinct_month.month_string} {$distinct_month.year},</a></li>
        {else}
          <li><a href="./monthly_{$distinct_month.month}_{$distinct_month.year}.html">{$distinct_month.month_string} {$distinct_month.year},</a></li>
        {/if}
      {/foreach}
      </ul>
    </dd>
  </dl>
</div>
{else}
  <p>{lang}No time records for this project{/lang}</p>
{/if}

{if is_foreachable($timerecords) && is_foreachable($companies)}
{foreach from=$companies item=company}
{assign var=total_hours value=0}
<div id="object_timerecords" class="object_info">
  <h3>{$company->getName()|clean}</h3>
  <table class="common_table">
  <tr>
    <th class="column_date">{lang}Date{/lang}</th>
    <th>{lang}User{/lang}</th>
    <th>{lang}Description{/lang}</th>
    <th class="column_billable">{lang}Billable{/lang}</th>
    <th class="column_hours">{lang}Hours{/lang}</th> 
  </tr>
  {foreach from=$timerecords item=timerecord}
    {assign var=current_user value=$timerecord->getUser()}
    {if date_in_range($timerecord->getRecordDate(), $start_date, $end_date) && $current_user->getCompanyId() == $company->getId()}
      {assign var=current_hours value=$timerecord->getValue()}
      {assign var=total_hours value=`$total_hours+$current_hours`}
      <tr>
        <td class="column_date">{$timerecord->getRecordDate()|date:0}</td>
        <td class="column_author"><a href="./user_{$current_user->getId()}.html">{$current_user->getName()}</a></td>
        <td class="description">
        {if instance_of($timerecord->getParent(), 'ProjectObject')}
          {project_exporter_object_link object=$timerecord->getParent() url_prefix='../'} 
          {if $timerecord->getBody()}
            &mdash; {$timerecord->getBody()|clean}
          {/if}
        {else}
          {$timerecord->getBody()|clean}
        {/if}
        </td>
        <td class="column_billable">
        {if $timerecord->isBillable()}
          {lang}Yes{/lang}
        {else}
          {lang}No{/lang}
        {/if}
        </td>
        <td class="column_hours"><b>{$timerecord->getValue()|number}</b></td>
      </tr>
    {/if}
  {/foreach}
      <tfoot>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td class="column_hours"><strong>{$total_hours|number}</strong></td>
        </tr>
      </tfoot>
  </table>
</div>
{/foreach}
{/if}