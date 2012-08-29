{title}New Predefined Invoice Item{/title}
{add_bread_crumb}New Item{/add_bread_crumb}

{form action=$add_template_url method=post autofocus=yes ask_on_leave=no }
  {include_template name=_invoice_item_template_form module=invoicing controller=invoice_item_templates_admin}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}