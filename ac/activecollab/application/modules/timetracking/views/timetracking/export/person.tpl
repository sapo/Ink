{assign var=company value=$current_person->getCompany()}
<div id="object_main_info" class="object_info">
  <h1>{lang}Timerecords{/lang}: {$current_person->getName()|clean}, {$company->getName()|clean}</h1>
</div>

{if is_foreachable($distinct_months) && is_foreachable($timerecords)}
{foreach from=$distinct_months item=distinct_month}
  {assign var=total_hours value=0}
  <div class="object_info">
    <h3>{$distinct_month.month_string}, {$distinct_month.year}</h3>
    <table class="common_table">
    <tr>
      <th class="column_date">{lang}Date{/lang}</th>
      <th class="description">{lang}Description{/lang}</th>
      <th class="column_billable">{lang}Billable{/lang}</th>
      <th class="column_hours">{lang}Hours{/lang}</th>
    </tr>
    {foreach from=$timerecords item=timerecord}
      {assign var=timerecord_user value=$timerecord->getUser()}
      {if date_in_range($timerecord->getRecordDate(), $distinct_month.beginning_of_month, $distinct_month.end_of_month) && $timerecord_user->getId()==$current_person->getId()}
        {assign var=current_hours value=$timerecord->getValue()}
        {assign var=total_hours value=`$total_hours+$current_hours`}
        <tr>
          <td class="column_date">{$timerecord->getRecordDate()|date:0}</td>
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
            <td class="column_hours"><strong>{$total_hours|number}</strong></td>
          </tr>
        </tfoot>
    </table>
   </div>
{/foreach}
{/if}