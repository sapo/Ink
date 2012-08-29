{title}New User{/title}
{add_bread_crumb}New User{/add_bread_crumb}

<div id="new_user">
  {form action=$active_company->getAddUserUrl() method=post}
    <div class="col">
    {wrap field=email}
      {label for=userEmail required=yes}Email{/label}
      {text_field name=user[email] value=$user_data.email id=userEmail class='required validate_email'}
    {/wrap}
    </div>
    
    {if $active_user->canChangeRole($logged_user)}
      <div class="col">
        {wrap field=role_id}
          {label for=userRole required=yes}Role{/label}
          {if $only_administrator}
            {role_name role=$active_user->getRole()}
            <input type="hidden" name="user[role_id]" value="{$user_data.role_id}" />
          {else}
            {select_role name=user[role_id] active_user=$active_user value=$user_data.role_id id=userRole class=required}
          {/if}
        {/wrap}
      </div>
    {/if}
    
    <div class="clear"></div>
    
    <p>{lang}The following properties are optional. You can set them now, or at any point in the future{/lang}:</p>
    
    <div id="additional_steps">
    
      <!-- Profile -->
      <div class="additional_step" id="additional_step_profile_details">
        <div class="head">
          <input type="checkbox" name="user[profile_details]" {if $user_data.profile_details}checked="checked"{/if} value="1" class="auto input_checkbox" id="userFormProfileDetails" /> {label for=userFormProfileDetails class=inline}Set user details, such as first and last name and company title{/label}
        </div>
        <div class="body">
          <div class="col">
          {wrap field=first_name}
            {label for=userFirstName}First Name{/label}
            {text_field name=user[first_name] value=$user_data.first_name id=userFirstName}
          {/wrap}
          </div>
          
          <div class="col">
          {wrap field=last_name}
            {label for=userLastName}Last Name{/label}
            {text_field name=user[last_name] value=$user_data.last_name id=userLastName}
          {/wrap}
          </div>
          <div class="clear"></div>
          
          <div class="col">
          {wrap field=title}
            {label for=userTitle}Title{/label}
            {text_field name='user[title]' value=$user_data.title id=userTitle}
          {/wrap}
          </div>
          <div class="clear"></div>
        </div>
      </div>
    
      <!-- Specify password -->
      <div class="additional_step" id="additional_step_password">
        <div class="head">
          <input type="checkbox" name="user[specify_password]" {if $user_data.specify_password}checked="checked"{/if} value="1" class="auto input_checkbox" id="userFormSetPassword" /> {label for=userFormSetPassword class=inline}Specify the account password. If not entered, then the system will a generate random password{/label}
        </div>
        <div class="body">
          <div class="col">
          {wrap field=password}
            {label for=userPassword}Password{/label}
            {password_field name='user[password]' value=$user_data.password id=userPassword}
          {/wrap}
          </div>
          
          <div class="col">
          {wrap field=password_a}
            {label for=userPasswordA}Retype{/label}
            {password_field name='user[password_a]' value=$user_data.password_a id=userPasswordA}
          {/wrap}
          </div>
          <div class="clear"></div>
        </div>
      </div>
      
      <!-- Send welcome message -->
      <div class="additional_step" id="additional_step_welcome_message">
        <div class="head">
          <input type="checkbox" name="user[send_welcome_message]" {if $user_data.send_welcome_message}checked="checked"{/if} value="1" class="auto input_checkbox" id="userFormSendWelcomeMessage" /> {label for=userFormSendWelcomeMessage class=inline}Send welcome email{/label}
        </div>
        <div class="body">
          <p>{lang}Personalize welcome message{/lang}:</p>
          {textarea_field name=user[welcome_message] id=userWelcomeMessage}{$user_data.welcome_message}{/textarea_field}
        </div>
      </div>
      
      {if $logged_user->isPeopleManager()}
      <!-- Set auto-assign settings -->
      <div class="additional_step" id="additional_step_auto_assign">
        <div class="head">
          <input type="checkbox" name="user[auto_assign]" {if $user_data.auto_assign}checked="checked"{/if} value="1" class="auto input_checkbox" id="userFormAutoAssign" /> {label for=userFormAutoAssign class=inline}Set this user to be automatically added to new projects{/label}
        </div>
        <div class="body">
          <p>{lang}Set a role or custom permissions to be used when user is automatically added to the project{/lang}:</p>
          {select_user_project_permissions name=user role_id=$user_data.auto_assign_role_id permissions=$user_data.auto_assign_permissions role_id_field=auto_assign_role_id permissions_field=auto_assign_permissions}
        </div>
      </div>
      {/if}
    </div>
  
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>