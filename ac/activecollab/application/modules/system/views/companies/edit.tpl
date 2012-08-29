{title}Update Information{/title}
{add_bread_crumb}Update Information{/add_bread_crumb}

{form action=$active_company->getEditUrl() method=post}
  {include_template name=_profile_form controller=companies module=system}
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}
