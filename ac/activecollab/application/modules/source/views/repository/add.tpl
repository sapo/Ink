{title}Add a repository{/title}
{add_bread_crumb}Add a repository{/add_bread_crumb}

<div id="repository_add">
  {form action=$repository_add_url method=post ask_on_leave=yes autofocus=yes}
    {include_template name=_repository_form module=source controller=repository}
  
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>