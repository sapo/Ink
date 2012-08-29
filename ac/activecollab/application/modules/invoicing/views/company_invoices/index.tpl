{title not_lang=yes}{lang name=$active_company->getName()}:name's Invoices{/lang}{/title}
{add_bread_crumb}Invoices{/add_bread_crumb}

<div class="list_view">
  <div class="object_list">
    <div class="hidden_overflow">
      {if $pagination && $pagination->getLastPage() > 1}
      <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=invoices page='-PAGE-'}{/pagination}</span></p>
      <div class="clear"></div>
      {/if}
    
      <p class="pagination top filter_group">
        <span class="inner_pagination">
          <a href="{assemble route=people_company_invoices company_id=$active_company->getId() status=active}" class="{if $status=='active'}active{/if}">{lang}Active{/lang}</a> |
          <a href="{assemble route=people_company_invoices company_id=$active_company->getId() status=paid}" class="{if $status=='paid'}active{/if}">{lang}Paid{/lang}</a> |
          <a href="{assemble route=people_company_invoices company_id=$active_company->getId() status=canceled}" class="{if $status=='canceled'}active{/if}">{lang}Canceled{/lang}</a>
        </span>
      </p>
    </div>
    
    <div id="company_invoices">
    {if is_foreachable($invoices)}
      <table class="invoices common_table">
        <tr>
          <td></td>
          <th class="invoice">{lang}Invoice #{/lang}</th>
          <th class="project">{lang}Project{/lang}</th>
          <th class="status">{lang}Status{/lang}</th>
          <th class="due_on">{lang}Due On{/lang}</th>
          <th class="pdf dont_print"></th>
        </tr>
      {foreach from=$invoices item=invoice}
        <tr class="{cycle values='odd,even'} {if $invoice->isOverdue()}overdue{/if}">
          <td>
            {if $invoice->isOverdue()}
              {image name=important.gif}
            {/if}
          </td>
          <td class="invoice">{invoice_link invoice=$invoice company=yes}</a></td>
          <td class="project">
          {if instance_of($invoice->getProject(), 'Project')}
            {project_link project=$invoice->getProject()}
          {else}
            --
          {/if}
          </td>
        {if $invoice->isIssued()}
          <td class="status">{action_on_by datetime=$invoice->getIssuedOn() user=$invoice->getIssuedBy() action='Issued' offset=0}</td>
          <td class="due_on">{$invoice->getDueOn()|date:0}</td>
          <td class="pdf dont_print"><a href="{$invoice->getCompanyPdfUrl()}" title="{lang}Download Invoice in PDF Format{/lang}"><img src="{image_url name=pdf-small.gif}" alt="" /></a></td>
        {elseif $invoice->isBilled()}
          <td class="status">{action_on_by datetime=$invoice->getClosedOn() user=$invoice->getClosedBy() action='Billed' offset=0}</td>
          <td class="due_on no_due_date">--</td>
          <td class="pdf dont_print"><a href="{$invoice->getCompanyPdfUrl()}" title="{lang}Download Invoice in PDF Format{/lang}"><img src="{image_url name=pdf-small.gif}" alt="" /></a></td>
        {elseif $invoice->isCanceled()}
          <td class="status">{action_on_by datetime=$invoice->getClosedOn() user=$invoice->getClosedBy() action='Canceled' offset=0}</td>
          <td class="due_on no_due_date">--</td>
          <td class="pdf dont_print">--</td>
        {/if}
        </tr>
      {/foreach}
      </table>
    {else}
      {if $status==active}
        <p class="empty_page">{lang name=$active_company->getName()}There are no invoices issued to :name{/lang}</p>
      {elseif $status==paid}
        <p class="empty_page">{lang name=$active_company->getName()}:name does not have any paid invoices{/lang}</p>
      {elseif $status==canceled}
        <p class="empty_page">{lang name=$active_company->getName()}:name does not have any canceled invoices{/lang}</p>
      {/if}
    {/if}
    </div>
  </div>
  {include_template name=tabs controller=company_invoices module=invoicing}
</div>