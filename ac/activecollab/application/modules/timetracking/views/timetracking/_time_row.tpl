<tr class="time_record {cycle values='odd,even'} {if $timerecord->isBilled()}billed{/if}">
  <td class="date">{$timerecord->getRecordDate()|date:0}</td>
  <td class="user">{user_link user=$timerecord->getUser()}</td>
  <td class="hours"><b>{$timerecord->getValue()}</b></td>
  <td class="desc">
  {if instance_of($timerecord->getParent(), 'ProjectObject')}
    {object_link object=$timerecord->getParent()} 
    {if $timerecord->getBody()}
      &mdash; {$timerecord->getBody()}
    {/if}
  {else}
    {$timerecord->getBody()}
  {/if}
  </td>
  <td class="billable">
  {if $timerecord->isBillable()}
    {lang}Yes{/lang}
  {else}
    {lang}No{/lang}
  {/if}
  </td>
  <td class="actions">
  {if $timerecord->canChangeBillableStatus($logged_user)}
    {if $timerecord->getBillableStatus() == BILLABLE_STATUS_BILLED}
      {link href=$timerecord->getUpdateBilledStateUrl(false) title='Billed...' class=mark_time_record_as_billed}<img src="{image_url name=dollar-small.gif}" alt="" />{/link} 
    {elseif  $timerecord->getBillableStatus() == BILLABLE_STATUS_BILLABLE}
      {link href=$timerecord->getUpdateBilledStateUrl(true) title='Not billed...' class=mark_time_record_as_billed}<img src="{image_url name=gray-dollar-small.gif}" alt="" />{/link} 
    {/if}
  {/if}
  
  {if $timerecord->canEdit($logged_user)}
    {link href=$timerecord->getEditUrl() title='Edit...' class=edit_time_record}<img src="{image_url name=gray-edit.gif}" alt="" />{/link} 
  {/if}
  </td>
  
  <td class="checkbox">
  {if $can_manage}
    <input type="checkbox" name="time_record_ids[]" value="{$timerecord->getId()}" class="auto slave_checkbox input_checkbox" />
  {/if}
  </td>
</tr>