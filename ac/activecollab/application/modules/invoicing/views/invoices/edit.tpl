{title}Update Invoice{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="edit_invoice">
  {form action=$active_invoice->getEditUrl() method=post id=edit_invoice_form block_labels=false}
    {include_template name=_invoice_form module=invoicing controller=invoices}

    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>
<script type="text/javascript">
  App.invoicing.InvoiceForm.init('edit_invoice_form','edit');
</script>
