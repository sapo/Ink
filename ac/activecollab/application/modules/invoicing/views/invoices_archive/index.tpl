{title}Archive{/title}
{add_bread_crumb}Statistics{/add_bread_crumb}

<div class="list_view" id="invoices_archive">
  <div class="object_list">
  {if is_foreachable($invoiced_companies)}
    <table>
      <tr>
        <th class="name">{lang}Client{/lang}</th>
        <th class="count">{lang}Total Invoices{/lang}</th>
        <th class="billed">{lang}Total Paid{/lang}</th>
      </tr>
    {foreach from=$invoiced_companies item=company_info}
      <tr class="{cycle values='odd,even'}">
        <td class="name"><a href="{assemble route=company_invoices company_id=$company_info.id}">{$company_info.name|clean}</td>
        <td class="count">{$company_info.invoices_count}</td>
        <td class="billed">$1230</td>
      </tr>
    {/foreach}
    </table>
  {else}
    <p class="empty_page">{lang}There are no archived invoices in the database{/lang}</p>
  {/if}
  </div>
  <ul class="category_list">
    <li><a href="{assemble route=invoices}"><span>{lang}Active Invoices{/lang}</span></a></li>
    <li><a href="{assemble route=invoice_payments}"><span>{lang}Payments{/lang}</span></a></li>
    <li class="selected"><a href="{assemble route=invoices_archive}"><span>{lang}Archive{/lang}</span></a></li>
  </ul>
  <div class="clear"></div>
</div>