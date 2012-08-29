{title}Add Users{/title}
{add_bread_crumb}Add{/add_bread_crumb}

<div id="add_people">
  {form action=$active_project->getAddPeopleUrl() method=post}
    <h2 class="section_name"><span class="section_name_span">{lang}Select Users{/lang}</span></h2>
    <div class="section_container">
      {wrap field=users class="select_users_add_permissions"}
      {if $logged_user->isOwner() || $logged_user->isAdministrator() || $logged_user->isProjectManager()}
        {select_users name=users exclude=$exclude_users}
      {else}
        {select_users name=users company=$logged_user->getCompany() exclude=$exclude_users}
      {/if}
      {/wrap}
      <div class="clear"></div>
    </div>
    <div id="select_permissions">
      <h2 class="section_name"><span class="section_name_span">{lang}Set Permissions{/lang}</span></h2>
      <div class="section_container">
        {select_user_project_permissions name=project_permissions}
      </div>
    </div>
  
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>