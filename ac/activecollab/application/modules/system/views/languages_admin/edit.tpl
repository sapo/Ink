{title}Update{/title}
{add_bread_crumb}Update{/add_bread_crumb}

{form action=$active_language->getEditUrl() method=post}
  {include_template name=_language_form controller=languages_admin module=system}
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}