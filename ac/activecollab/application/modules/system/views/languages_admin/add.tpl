{title}New Language{/title}
{add_bread_crumb}New Language{/add_bread_crumb}

{form action='?route=admin_languages_add' method=post}
  {include_template name=_language_form controller=languages_admin module=system}
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}