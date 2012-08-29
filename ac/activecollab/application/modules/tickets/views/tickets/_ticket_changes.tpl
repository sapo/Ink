{if is_foreachable($_changes)}
<div id="ticket_changes" class="resource object_section">
  <div class="head">
    <h2 class="section_name"><span class="section_name_span">{lang}History{/lang}</span></h2>
  </div>
  <div class="body">
    <div id="ticket_changes_wrapper">
    {foreach from=$_changes item=_change name=ticket_changes}
      <div class="ticket_change">
        <h3>{action_on_by user=$_change->getCreatedBy() datetime=$_change->getCreatedOn() action=Updated}</h3>
        <ul>
        {foreach from=$_change->getVerboseChanges() item=_field_change}
          <li>{$_field_change}</li>
        {/foreach}
        </ul>
      </div>
    {/foreach}
    </div>
  
  {if $_total_changes > 3}
    <p id="show_all_ticket_changes"><a href="{$active_ticket->getChangesUrl()}">{lang total=$_total_changes}Show all :total changes{/lang}</a></p>
  {/if}
  </div>
</div>
{/if}