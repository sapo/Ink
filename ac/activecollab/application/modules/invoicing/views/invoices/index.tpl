{if $status==issued}
  {title}Issued Invoices{/title}
  {add_bread_crumb}Issued{/add_bread_crumb}
{elseif $status==drafts}
  {title}Invoice Drafts{/title}
  {add_bread_crumb}Drafts{/add_bread_crumb}
{elseif $status==billed}
  {title}Billed Invoices{/title}
  {add_bread_crumb}Billed{/add_bread_crumb}
{elseif $status==canceled}
  {title}Canceled Invoices{/title}
  {add_bread_crumb}Canceled{/add_bread_crumb}
{/if}

<div class="list_view" id="invoices">
  <div class="object_list">
  <div class="hidden_overflow">
    {if $pagination && $pagination->getLastPage() > 1}
    <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=invoices page='-PAGE-'}{/pagination}</span></p>
    <div class="clear"></div>
    {/if}

    <p class="pagination top filter_group">
      <span class="inner_pagination">
        <a href="{assemble route=invoices status=issued}" class="{if $status=='issued'}active{/if}">{lang}Issued{/lang}</a> |
        <a href="{assemble route=invoices status=drafts}" class="{if $status=='drafts'}active{/if}">{lang count=$drafts_count}Drafts (:count){/lang}</a> |
        <a href="{assemble route=invoices status=billed}" class="{if $status=='billed'}active{/if}">{lang}Billed{/lang}</a> |
        <a href="{assemble route=invoices status=canceled}" class="{if $status=='canceled'}active{/if}">{lang}Canceled{/lang}</a>
      </span>
    </p>
  </div>
  
  {if is_foreachable($invoices)}
    <table class="invoices common_table">
      <tr>
        <th></th>
        <th class="invoice">{lang}Invoice #{/lang}</td>
        <th class="client">{lang}Client{/lang}</td>
        <th class="comment">{lang}Our Comment{/lang}</td>
        <th class="total">{lang}Total{/lang}</td>
        <th class="options"></td>
      </tr>
    {foreach from=$invoices item=invoice}
      <tr class="{cycle values='odd,even'} {if $invoice->isOverdue()}overdue{/if}">
        <td>
          {if $invoice->isOverdue()}
            {image name=important.gif}
          {/if}
        </td>
        <td class="invoice"><a href="{$invoice->getViewUrl()}">{$invoice->getName(true)|clean}</a></td>
        <td class="client">{company_link company=$invoice->getCompany()}</td>
        <td class="comment">{$invoice->getComment()|clean}</td>
        <td class="total">{$invoice->getTaxedTotal()|number:2} {$invoice->getCurrencyCode()}</td>
        <td class="options">{link href=$invoice->getAddPaymentUrl() title='Add Payment'}<img src="{image_url name=add-payment.gif module=invoicing}" alt="" />{/link} {link href=$invoice->getViewUrl() title='View Invoice Details'}<img src="{image_url name=gray-edit.gif}" alt="" />{/link}</td>
      </tr>
    {/foreach}
    </table>
    {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
    <p class="next_page"><a href="{assemble route=invoices page=$pagination->getNextPage()}">Next Page</a></p>
    {/if}
  {else}
    <p class="empty_page">{lang}There are no invoices on this page{/lang}{if $add_invoice_url}. {lang add_url=$add_invoice_url}Would you like to <a href=":add_url">create one</a>?{/lang}{/if}</p>
  {/if}
  </div>
  
  {include_template name=tabs controller=invoices module=invoicing}
</div>