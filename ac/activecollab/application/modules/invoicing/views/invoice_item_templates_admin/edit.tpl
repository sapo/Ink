{title description=$active_invoice_item_template->getDescription}Edit Item: :description{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_item_template->getEditUrl() method=post autofocus=yes ask_on_leave=no }
  {include_template name=_invoice_item_template_form module=invoicing controller=invoice_item_templates_admin}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}