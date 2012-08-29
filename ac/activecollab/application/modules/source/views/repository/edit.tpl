{title}Edit repository{/title}
{add_bread_crumb}Edit repository{/add_bread_crumb}

<div id="repository_edit">
  {form action=$active_repository->getEditurl() method=post ask_on_leave=yes autofocus=yes}
    {include_template name=_repository_form module=source controller=repository}
  
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>