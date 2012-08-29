{title}New Payment{/title}
{add_bread_crumb}New Payment{/add_bread_crumb}

<div id="add_invoice_payment">
  {form action=$active_invoice->getAddPaymentUrl() method=post}
    {include_template name=_payment_form module=invoicing controller=invoice_payments}
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>