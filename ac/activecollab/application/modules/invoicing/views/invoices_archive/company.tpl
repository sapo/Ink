{title name=$company->getName()}:name's Billed / Canceled Invoices{/title}
{add_bread_crumb}Invoices{/add_bread_crumb}

<div class="list_view" id="archived_company_invoices">
  <div class="object_list">
  
  <div class="hidden_overflow">
    <p class="pagination top filter_group">
      <span class="inner_pagination">
        <a href="{assemble route=company_invoices company_id=$company->getId()}" class="{if $status=='billed'}active{/if}">{lang}Billed{/lang}</a> |
        <a href="{assemble route=company_invoices company_id=$company->getId() status=canceled}" class="{if $status=='canceled'}active{/if}">{lang}Canceled{/lang}</a>
      </span>
    </p>
  </div>
   
  {if is_foreachable($invoices)}
    {foreach from=$invoices item=invoices_group}
      {if is_foreachable($invoices_group.invoices)}
        <h2 class="new_section_name">{$invoices_group.currency->getName()|clean}</h2>
        <div class="section_container">
          <table class="invoices">
            <tr>
              <th class="invoice_id">{lang}Invoice #{/lang}</th>
              <th class="comment">{lang}Our Comment{/lang}</th>
              <th class="billed_on">{lang}Billed / Canceled On{/lang}</th>
              <th class="total">{lang}Total{/lang}</th>
            </tr>
            {foreach from=$invoices_group.invoices item=invoice}
            <tr class="{cycle values='odd,even'}">
              <td class="invoice_id"><a href="{$invoice->getViewUrl()}">{$invoice->getName(true)|clean}</a></td>
              <td class="comment">{$invoice->getComment()|clean}</td>
              <td class="billed_on">{$invoice->getClosedOn()|date}</td>
              <td class="total">{$invoice->getTotal()|number:2} {$invoice->getCurrencyCode()}</td>
            </tr>
            {/foreach}
          </table>
        </div>
      {/if}
    {/foreach}
  {else}
    <p>{lang}There are no archived company invoices to show{/lang}</p>
  {/if}
  </div>
  {include_template name=tabs controller=invoices module=invoicing}
  <div class="clear"></div>
</div>