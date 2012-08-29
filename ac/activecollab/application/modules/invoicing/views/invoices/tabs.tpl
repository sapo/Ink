<ul class="category_list">
  <li {if $request->getController() == 'invoices' && $request->getAction() == 'index'}class="selected"{/if}><a href="{assemble route=invoices}"><span>{lang}Invoices{/lang}</span></a></li>
  <li {if $request->getController() == 'invoice_payments'}class="selected"{/if}><a href="{assemble route=invoice_payments}"><span>{lang}Payments{/lang}</span></a></li>
  <li {if ($request->getController() == 'invoices' && $request->getAction() == 'archive') || ($request->getController() == 'invoices_archive')}class="selected"{/if}><a href="{assemble route=invoices_archive}"><span>{lang}Archive{/lang}</span></a></li>
</ul>
<div class="clear"></div>