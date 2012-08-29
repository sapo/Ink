{title}New Invoice{/title}
{add_bread_crumb}New Invoice{/add_bread_crumb}

<div id="add_invoice">
  {form action=$add_invoice_url method=post id=add_invoice_form block_labels=false}
    {include_template name=_invoice_form module=invoicing controller=invoices}

    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>
<script type="text/javascript">
  App.invoicing.InvoiceForm.init('add_invoice_form','add');
</script>