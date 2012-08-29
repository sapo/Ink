{title}Update Settings{/title}
{add_bread_crumb}Update Settings{/add_bread_crumb}

<div id="edit_user_settings">
  {form action=$active_user->getEditSettingsUrl() method=post autofocus=no}
    <fieldset>
      <h2 class="section_name"><span class="section_name_span">{lang}Locale{/lang}</span></h2>
      <div class="section_container">
      {if LOCALIZATION_ENABLED}
        <div class="col">
        {wrap field=language}
          {label for=userLanguage}Language{/label}
          {select_language name='user[language]' value=$user_data.language optional=yes id=userLanguage}
        {/wrap}
        </div>
      {/if}
        
        <div class="col">
        {wrap field=format_date}
          {label for=userFormatDate}Date Format{/label}
          {select_datetime_format name='user[format_date]' value=$user_data.format_date optional=yes id=userFormatDate mode=date}
        {/wrap}
        </div>
        
        <div class="col">
        {wrap field=format_time}
          {label for=userFormatTime}Time Format{/label}
          {select_datetime_format name='user[format_time]' value=$user_data.format_time optional=yes id=userFormatTime mode=time}
        {/wrap}
        </div>
      </div>
    </fieldset>
    
    <fieldset>
      <h2 class="section_name"><span class="section_name_span">{lang}Date and Time{/lang}</span></h2>
      <div class="section_container">
        <div class="col">
        {wrap field=first_weekday}
          {label for=userFirstWeekday}First Day of the Week{/label}
          {select_week_day name='user[time_first_week_day]' value=$user_data.time_first_week_day id=userFirstWeekday}
        {/wrap}
        </div>
        
        <div class="col">
        {wrap field=timezone}
          {label for=userTimezone}Timezone{/label}
          {select_timezone name='user[time_timezone]' value=$user_data.time_timezone optional=no id=userTimezone}
        {/wrap}
        </div>
        
        <div class="col">
        {wrap field=dst}
          {label for=userDST}Daylight Saving Time{/label}
          {yes_no_default name='user[time_dst]' value=$user_data.time_dst default=$default_dst_value id=userDST}
        {/wrap}
        </div>
      </div>
    </fieldset>
    
    <fieldset>
      <h2 class="section_name"><span class="section_name_span">{lang}Miscellaneous{/lang}</span></h2>
      <div class="section_container">
        <div class="col">
        {wrap field=default_assignments_filter}
          {label for=defaultAssignmentsFilter}Default Assignments Filter{/label}
          {select_default_assignment_filter name='user[default_assignments_filter]' value=$user_data.default_assignments_filter id=defaultAssignmentsFilter optional=yes user=$active_user}
        {/wrap}
        </div>
      
        <div class="col">
        {wrap field=theme}
          {label for=userTheme}Theme{/label}
          {select_theme name=user[theme] value=$user_data.theme id=userTheme optional=yes}
        {/wrap}
        </div>
        
        <div class="col">
        {wrap field=visual_editor}
          {label for=userVisualEditor}Visual Editor{/label}
          {yes_no name=user[visual_editor] value=$user_data.visual_editor id=userVisualEditor}  
        {/wrap}
        </div>
      </div>
    </fieldset>
    
    {if $logged_user->isPeopleManager()}
    <fieldset>
      <h2 class="section_name"><span class="section_name_span">{lang}Automatically Add to New Project{/lang}</span></h2>
      <div class="section_container" id="auto_assign_user">
        {wrap field=auto_assign}
          {yes_no name=user[auto_assign] value=$user_data.auto_assign id=userAutoAssign}  
          <p class="details">{lang}Select <b>Yes</b> to have this user automatically added to each new project{/lang}</p>
        {/wrap}
        
        <div id="auto_assign_role_and_permissions" {if !$user_data.auto_assign}style="display: none"{/if}>
          <p>{lang}Please select a role or set custom permissions for user in this project{/lang}:</p>
          {select_user_project_permissions name=user role_id=$user_data.auto_assign_role_id permissions=$user_data.auto_assign_permissions role_id_field=auto_assign_role_id permissions_field=auto_assign_permissions}
        </div>
      </div>
    </fieldset>
    {/if}
  
    {wrap_buttons}
    	{submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>