{title}New Company{/title}
{add_bread_crumb}New Company{/add_bread_crumb}

{form action='?route=people_companies_add' method=post}
  {include_template name=_profile_form controller=companies module=system}

  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}