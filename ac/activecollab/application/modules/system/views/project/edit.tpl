{title}Edit project{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_project->getEditUrl() method=post}
  {include_template module=system controller=project name=_project_form}
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}