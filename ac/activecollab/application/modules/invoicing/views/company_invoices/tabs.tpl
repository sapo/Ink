<ul class="category_list">
  <li {if $request->getController() == 'company_invoices' && $request->getAction() == 'index'}class="selected"{/if}><a href="{assemble route=people_company_invoices company_id=$active_company->getId()}"><span>{lang}Invoices{/lang}</span></a></li>
  <li {if $request->getController() == 'company_invoices' && $request->getAction() == 'payments'}class="selected"{/if}><a href="{assemble route=people_company_invoices_payments company_id=$active_company->getId()}"><span>{lang}Payments{/lang}</span></a></li>
</ul>
<div class="clear"></div>