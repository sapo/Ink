{title}Update Payment{/title}
{add_bread_crumb}Update Payment{/add_bread_crumb}

<div id="edit_invoice_payment">
  {form action=$active_invoice_payment->getEditUrl() method=post}
    {include_template name=_payment_form module=invoicing controller=invoice_payments}
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>