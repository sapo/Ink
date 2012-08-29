{title}Add to Projects{/title}
{add_bread_crumb}Add to Projects{/add_bread_crumb}

<div id="add_user_to_projects">
  {form action=$active_user->getAddToProjectsUrl() method=post}
    {wrap field=projects}
      {label for=addToProjectsProjects required=yes}Projects{/label}
      {select_projects name=add_to_projects[projects] value=$add_to_projects_data.projects user=$logged_user exclude=$exclude_project_ids}
    {/wrap}
    
    <p>{lang name=$active_user->getDisplayName()}Select permissions you wish :name to have on these projects{/lang}:</p>
    
    {wrap field=project_permissions}
      {select_user_project_permissions name=add_to_projects role_id=$add_to_projects_data.role_id permissions=$add_to_projects_data.permissions role_id_field=role_id permissions_field=permissions}
    {/wrap}
  
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>