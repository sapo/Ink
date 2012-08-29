{title}Changes{/title}
{add_bread_crumb}Changes{/add_bread_crumb}

{if !$request->isAsyncCall()}
<div id="ticket_changes">
{/if}

{if is_foreachable($changes)}
{foreach from=$changes item=change name=ticketchanges}
  <div class="ticket_change">
    <h3>{action_on_by user=$change->getCreatedBy() datetime=$change->getCreatedOn() action=Updated}</h3>
    <ul>
    {foreach from=$change->getVerboseChanges() item=_fieldchange}
      <li>{$_fieldchange}</li>
    {/foreach}
    </ul>
  </div>
{/foreach}
{else}
  <p class="empty_page">{lang}This ticket has not been changed{/lang}.</p>
{/if}

{if !$request->isAsyncCall()}
</div>
{/if}