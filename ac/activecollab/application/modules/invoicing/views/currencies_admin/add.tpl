{title}New Currency{/title}
{add_bread_crumb}New{/add_bread_crumb}

<div id="add_currency">
  {form action=$add_currency_url method=post}
    {include_template name=_currency_form module=invoicing controller=currencies_admin}
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>