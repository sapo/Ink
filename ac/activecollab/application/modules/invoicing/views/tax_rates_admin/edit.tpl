{title}Update Currency{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="add_tax_rate">
  {form action=$active_tax_rate->getEditUrl() method=post}
    {include_template name=_tax_rate_form module=invoicing controller=tax_rates_admin}

    {wrap_buttons}
      {submit}Submit {/submit}
    {/wrap_buttons}
  {/form}
</div>