{title}New Project{/title}
{add_bread_crumb}New project{/add_bread_crumb}

<div id="add_project_form">
  {form action='?route=projects_add' method=post}
    {include_template module=system controller=project name=_project_form}
    
    {wrap field=project_template_id}
      {label for=projectTemplate}Project Template{/label}
      {select_project_template name=project[project_template_id] value=$project_data.project_template_id id=projectTemplate}
      <p id="users_from_auto_assignment" class="details" {if $project_data.project_template_id}style="display: none"{/if}>{lang}People will be added to the project based on auto-assignment settings{/lang}</p>
      <p id="users_from_template" class="details" {if !$project_data.project_template_id}style="display: none"{/if}>{lang}People will be imported from the template and auto-assignment settings will be ignored{/lang}</p>
    {/wrap}
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>