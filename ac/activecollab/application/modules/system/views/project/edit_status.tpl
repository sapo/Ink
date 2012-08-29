{title}Change Status{/title}
{add_bread_crumb}Change status{/add_bread_crumb}

{form action=$active_project->getEditStatusUrl() method=post}
  {wrap field=status}
    {label for=projectStatus required=yes}Status{/label}
    {select_project_status name='project[status]' value=$project_data.status id=projectStatus}
  {/wrap}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}