<div id="object_main_info" class="object_info">
  <h1>{lang}Timerecords{/lang}</h1>
</div>

{if is_foreachable($distinct_months)}
<div id="object_details">
  <dl class="properties">
    <dt>{lang}Month{/lang}:</dt>
    <dd>
      <ul class="category_list">
        {if !$current_month}
          <li class="selected"><a href="./index.html">{lang}All Months{/lang},</a></li>
        {else}
          <li><a href="./index.html">{lang}All Months{/lang},</a></li>
        {/if}
      {foreach from=$distinct_months item=distinct_month}
        <li><a href="./monthly_{$distinct_month.month}_{$distinct_month.year}.html">{$distinct_month.month_string} {$distinct_month.year},</a></li>
      {/foreach}
      </ul>
    </dd>
  </dl>
</div>
{else}
  <p>{lang}No time records for this project{/lang}</p>
{/if}

{if is_foreachable($companies)}
<div class="object_info">
    {foreach from=$companies item=company}
      <h3>{$company->getName()|clean}</h3>
      <table class="common_table">
      {foreach from=$people item=person}
      {if $person->getCompanyId() == $company->getId()}
        <tr>
          <td><a href="./user_{$person->getId()}.html">{$person->getName()|clean}</a></td>
          <td class="column_time_report">{$person->temp_total_time|number} {lang}hours total{/lang}</td>
          <td class="column_options"><a href="./user_{$person->getId()}.html">{lang}Report{/lang}</a></td>
        </tr>
      {/if}
      {/foreach}
      </table>
    {/foreach}
</div>
{/if}