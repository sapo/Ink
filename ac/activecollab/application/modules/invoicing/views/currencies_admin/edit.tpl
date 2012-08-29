{title}Update Currency{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="add_currency">
  {form action=$active_currency->getEditUrl() method=post}
    {include_template name=_currency_form module=invoicing controller=currencies_admin}
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>