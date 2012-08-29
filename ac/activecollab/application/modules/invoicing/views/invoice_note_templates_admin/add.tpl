{title}Add Invoicing Note{/title}
{add_bread_crumb}New Invoicing Note{/add_bread_crumb}

{form action=$add_note_url method=POST}
  {include_template name=_note_form module=invoicing controller=invoice_note_templates_admin}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}