{title}General Settings{/title}
{add_bread_crumb}General Settings{/add_bread_crumb}

{form action='?route=admin_settings_general' method=post}
  {wrap field=show_welcome_message}
    {label}Show Welcome Message{/label}
    {yes_no name='general[show_welcome_message]' value=$general_data.show_welcome_message}
    <p class="details">{lang}Welcome message provides a simple walkthrough instructions for setting up activeCollab. It is great for fresh installation. To turn it on set this option to Yes{/lang}.</p>
  {/wrap}
  
  {wrap field=projects_use_client_icons}
    {label}Use Client Logo for Projects Without Custom Icons{/label}
    {yes_no name='general[projects_use_client_icons]' value=$general_data.projects_use_client_icons}
    <p class="details">{lang}Set this option to Yes if you wish that projects which do not have custom icons use clients logo instead of the default project icon{/lang}.</p>
  {/wrap}
  
  {wrap field=theme}
    {label for=generalTheme}Default Theme{/label}
    {select_theme name='general[theme]' value=$general_data.theme id=generalTheme optional=no}
  {/wrap}
  
  {wrap field=default_assignments_filter}
    {label for=defaultAssignmentsFilter}Default Assignments Filter{/label}
    {select_default_assignment_filter name='general[default_assignments_filter]' value=$general_data.default_assignments_filter id=defaultAssignmentsFilter optional=no}
  {/wrap}
  
  {wrap field=project_templates_group}
    {label for=generalProjectGroup}Project Templates Group{/label}
    {select_project_group name='general[project_templates_group]' value=$general_data.project_templates_group optional=yes id=generalProjectGroup can_create_new=no}
    <p class="details">{lang}Only treat projects from selected group as project templates. If no group is selected all projects will be treated as potential templates{/lang}.</p>
  {/wrap}
  
  {wrap field=on_logout_url}
    {label}When User Logs Out{/label}
    <div><input type="radio" name="use_on_logout_url" class="auto input_radio" value="0" id="generalUseLogoutUrlNo" {if !$general_data.use_on_logout_url}checked="checked"{/if} /> {label for=generalUseLogoutUrlNo class=inline}Redirect him back to login page{/label}</div>
    <div><input type="radio" name="use_on_logout_url" class="auto input_radio" value="1" id="generalUseLogoutUrlYes" {if $general_data.use_on_logout_url}checked="checked"{/if} /> {label for=generalUseLogoutUrlYes class=inline}Redirect him to a custom URL{/label}</div>
    <div id="on_logout_url_container">
      {text_field name=general[on_logout_url] value=$general_data.on_logout_url id=on_logout_url}
      <p class="details block">{lang}Specify URL user will be redirected to when he logs out{/lang}</p>
    </div>
  {/wrap}
  
  {wrap_buttons}
	  {submit}Submit{/submit}
  {/wrap_buttons}
{/form}