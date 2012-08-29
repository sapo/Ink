{title not_lang=yes}{lang name=$active_company->getName()}:name's Payments{/lang}{/title}
{add_bread_crumb}Payments{/add_bread_crumb}

<div class="list_view">
  <div class="object_list">
    {if is_foreachable($payments)}
      <table class="payments">
        <tr>
          <th class="invoice">{lang}Invoice{/lang}</th>
          <th class="amount">{lang}Amount{/lang}</th>
          <th class="paid_on">{lang}Paid On{/lang}</th>
        </tr>
      {foreach from=$payments item=payment}
        {assign var=payment_invoice value=$payment->getInvoice()}
        <tr class="{cycle values='odd,even'}">
          <td class="invoice">{invoice_link invoice=$payment_invoice company=yes}</td>
          <td class="amount">{$payment->getAmount()} {$payment_invoice->getCurrencyCode()|clean}</td>
          <td class="paid_on">{$payment->getPaidOn()|date}</td>
        </tr>
      {/foreach}
      </table>
    {else}
      <p class="empty_page">{lang name=$active_company->getName()}:name has not made any payments{/lang}</p>
    {/if}
  </div>
  {include_template name=tabs controller=company_invoices module=invoicing}
</div>