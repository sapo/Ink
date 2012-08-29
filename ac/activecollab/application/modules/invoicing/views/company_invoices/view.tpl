{title not_lang=yes}{$active_invoice->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

{title not_lang=yes}{$active_invoice->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

<div id="invoice_details">
{if $active_invoice->getStatus() != INVOICE_STATUS_CANCELED}
  <ul class="object_options">
    <li><a href="{$active_invoice->getCompanyPdfUrl()}"><span>{lang}Download PDF{/lang}</span></a></li>
  </ul>
{/if}
  <div class="main_object">
    <div class="body">
      <dl class="properties">
        <dt>{lang}Client{/lang}</dt>
        <dd>{company_link company=$active_invoice->getCompany()}</dd>

      {if instance_of($active_invoice->getProject(), 'Project')}
        <dt>{lang}Project{/lang}</dt>
        <dd>{project_link project=$active_invoice->getProject()}</dd>
      {/if}
        <dt>{lang}Status{/lang}</dt>
        <dd>
        {if $active_invoice->isIssued()}
          {action_on_by action=Issued datetime=$active_invoice->getIssuedOn() user=$active_invoice->getIssuedBy() format=date offset=0}. {lang}Due on{/lang} {$active_invoice->getIssuedOn()|date:0}
        {elseif $active_invoice->isBilled()}
          {action_on_by action=Billed datetime=$active_invoice->getClosedOn() user=$active_invoice->getClosedBy() format=date offset=0}
        {elseif $active_invoice->isCanceled()}
          {action_on_by action=Canceled datetime=$active_invoice->getClosedOn() user=$active_invoice->getClosedBy() format=date offset=0}
        {else}
          {lang}Draft{/lang}
        {/if}
        </dd>
      </dl>

      <div class="resources">
        <!-- Items -->
        <div class="resource">
          <div class="body">
          {if is_foreachable($active_invoice->getItems())}
            <table class="items">
              <tr>
                <th class="num"></th>
                <th class="description">{lang}Description{/lang}</th>
                <th class="quantity">{lang}Qty.{/lang}</th>
                <th class="unit_cost">{lang}Unit Cost{/lang}</th>
                <th class="tax_rate">{lang}Tax{/lang}</th>
                <th class="total">{lang}Total{/lang}</th>
              </tr>
            {foreach from=$active_invoice->getItems() item=invoice_item}
              <tr class="{cycle values='odd,even'}">
                <td class="num">#{$invoice_item->getPosition()}</td>
                <td class="description">{$invoice_item->getDescription()|clean}</td>
                <td class="quantity">{$invoice_item->getQuantity()}</td>
                <td class="unit_cost">{$invoice_item->getUnitCost()}</td>
                <td class="tax_rate">{$invoice_item->getTaxRateName()|clean}</td>
                <td class="total">{$invoice_item->getTotal()|number:2}</td>
              </tr>
            {/foreach}
            </table>

            <dl class="invoice_summary">
              <dt class="total">{lang}Total{/lang}:</dt>
              <dd class="total">{$active_invoice->getTotal()|number:2}</dd>

              <dt class="tax">{lang}Tax{/lang}:</dt>
              <dd class="tax">{$active_invoice->getTax()|number:2}</dd>

              <dt class="total_cost">{lang}Total Cost{/lang}:</dt>
              <dd class="total_cost">{$active_invoice->getTaxedTotal()|number:2} {$active_invoice->getCurrencyCode()|clean}</dd>
            </dl>
          {else}
            <p class="empty_page">{lang}This invoice has no items{/lang}</p>
          {/if}
          
          {if $active_invoice->getNote()}
            <div class="invoice_note">
              <p class="bold">{lang}Note{/lang}:</p>
              {$active_invoice->getNote()|clean|nl2br}
            </div>
          {/if}
          </div>
        </div>

      {if $active_invoice->getStatus() == INVOICE_STATUS_ISSUED || $active_invoice->getStatus() == INVOICE_STATUS_BILLED}
        <!-- Payments -->
        <div class="resource">
          <h2 class="section_name"><span class="section_name_span">{lang}Payments{/lang}</span></h2>
          
          <div class="body">
          {if is_foreachable($active_invoice->getPayments())}
            <table class="payments">
              <tr>
                <th class="paid_on">{lang}Paid On{/lang}</th>
                <th class="amount">{lang}Amount{/lang}</th>
              </tr>
            {foreach from=$active_invoice->getPayments() item=payment}
              <tr class="{cycle values='odd,even'}">
                <td class="paid_on">{$payment->getPaidOn()|date:0}</td>
                <td class="amount">{$payment->getAmount()|number:2} {$active_invoice->getCurrencyCode()|clean}</td>
              </tr>
            {/foreach}
              <tr>
                <td class="right"></td>
                <td class="total_paid">{lang}Total{/lang}: {$active_invoice->getPaidAmount()|number:2} {$active_invoice->getCurrencyCode()} ({$active_invoice->getPercentPaid()}%)</td>
                <td></td>
              </tr>
            </table>
          {else}
            <p class="empty_page">{lang}There are no payments for this invoice{/lang}</p>
          {/if}
          </div>
        </div>
      {/if}
      </div>
    </div>
  </div>
</div>