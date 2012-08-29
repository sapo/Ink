<div id="empty_slate_timetracking_time" class="empty_slate">
  <h3>{lang}About Invoice Time{/lang}</h3>
  
  <ul class="icon_list">
    <li>
      <img src="{image_url name=settings/date_time.gif module=system}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}Related Time Records{/lang}</span>
      <span class="icon_list_description">{lang}All time records related to this invoice will be automatically marked as "Pending Payment" when this invoice gets issued. When invoice is marked as billed, then all related time records will be automatically marked as billed, too. When the invoice is canceled, all related records will be automatically reverted to their original, billable state and released{/lang}.</span>
    </li>
    
    <li>
      <img src="{image_url name=release-big.gif module=invoicing}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}On Releasing Time Records{/lang}</span>
      <span class="icon_list_description">{lang}When records are released, relation between this invoice and them is removed, without any records being deleted. Instead, releated records will be reverted to their original, billable state and invoice will not change their status in the future{/lang}.</span>
    </li>
  </ul>
</div>