{title}Move{/title}
{add_bread_crumb}Move{/add_bread_crumb}

<p>{lang type=$active_object->getVerboseType(true) name=$active_object->getName() url=$active_object->getViewUrl() project=$active_project->getName() project_url=$active_project->getOverviewUrl()()}You are about to move :type <a href=":url">:name</a> from <a href=":project_url">:project</a> project. Please select destination project:{/lang}</p>

{form action=$active_object->getMoveUrl() method=post}
  {wrap field=project_id}
    {label id=move_to_project required=yes}Move to Project{/label}
    {select_project name=move_to_project_id user=$logged_user exclude=$active_project->getId() class=required}
  {/wrap}
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}