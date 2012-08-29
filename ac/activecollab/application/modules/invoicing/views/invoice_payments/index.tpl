{title}Payments{/title}
{add_bread_crumb}Payments{/add_bread_crumb}

<div class="list_view" id="invoice_payments">
  <div class="object_list">
  {if is_foreachable($payments)}
    {if $pagination->getLastPage() > 1}
    <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=invoice_payments page='-PAGE-'}{/pagination}</span></p>
    <div class="clear"></div>
    {/if}
    
    {foreach from=$payments key=year item=months}
      {foreach from=$months key=month_name item=month_payments}
        <h2 class="section_name"><span class="section_name_span">{$month_name|clean} {$year}</span></h2>
        <div class="section_container">
          <table class="payments">
            <tr>
              <th class="invoice">{lang}Invoice #{/lang}</td>
              <th class="client">{lang}Paid on{/lang}</td>
              <th class="amount">{lang}Amount{/lang}</td>
              <th class="options"></td>
            </tr>
          {foreach from=$month_payments item=payment}
            {assign var=payment_invoice value=$payment->getInvoice()}
            <tr class="{cycle values='odd,even'}">
              <td class="invoice">{invoice_link invoice=$payment_invoice}</a></td>
              <td class="invoice">{$payment->getPaidOn()|date}</a></td>
              <td class="amount">{$payment->getAmount()|number:2} {$payment_invoice->getCurrencyCode()|clean}</td>
              <td class="options">{if $payment->canEdit($logged_user)}{link href=$payment->getEditUrl()}<img src="{image_url name=gray-edit.gif}" alt="" />{/link}{/if} {if $payment->canDelete($logged_user)}{link href=$payment->getDeleteUrl() method=post confirm='Are you sure that you want to mark this payment as deleted?'}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}{/if}</td>
            </tr>
          {/foreach}
          </table>
        </div>
      {/foreach}
    {/foreach}
    
    {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
    <p class="next_page"><a href="{assemble route=invoice_payments page=$pagination->getNextPage()}">Next Page</a></p>
    {/if}
  {else}
      <p class="empty_page">{lang}There are no payments in the database{/lang}</p>
  {/if}
  </div>
  
  {include_template name=tabs controller=invoices module=invoicing}
</div>