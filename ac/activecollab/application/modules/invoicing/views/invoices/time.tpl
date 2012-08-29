{title not_lang=yes}{lang name=$active_invoice->getName()}:name Time{/lang}{/title}
{add_bread_crumb}Time{/add_bread_crumb}

<div id="invoice_time">
{if is_foreachable($time_records)}
  <div id="timerecords">
    <table class="common_table">
      <tr>
        <th class="date">{lang}Date{/lang}</th>
        <th class="user">{lang}User{/lang}</th>
        <th class="hours">{lang}Hours{/lang}</th>
        <th class="description">{lang}Description{/lang}</th>
        <th class="project">{lang}Project{/lang}</th>
      </tr>
    {foreach from=$time_records item=time_record}
      <tr>
        <td class="date">{$time_record->getRecordDate()|date:0}</td>
        <td class="user">{user_link user=$time_record->getUser()}</td>
        <td class="hours"><b>{$time_record->getValue()}</b></td>
        <td class="description">
        {if instance_of($time_record->getParent(), 'ProjectObject')}
          {object_link object=$time_record->getParent()} 
          {if $time_record->getBody()}
            &mdash; {$time_record->getBody()}
          {/if}
        {else}
          {$time_record->getBody()}
        {/if}
        </td>
        <td class="project">{project_link project=$time_record->getProject()}</td>
      </tr>
    {/foreach}
    </table>
  </div>
  <p id="release_invoice_time_records">{link href=$active_invoice->getReleaseTimeUrl() method=post confirm='Are you sure that you want to remove relation between this invoice and time records listed above? Note that time records will NOT be deleted!'}Release Records{/link}</p>
  {empty_slate name=time module=invoicing}
{else}
  <p class="empty_page">{lang}There is no time attached to this invoice{/lang}</p>
{/if}
</div>