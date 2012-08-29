{title}Update Icon{/title}
{add_bread_crumb}Update Icon{/add_bread_crumb}

<div id="edit_icon">
  <form method="POST" action="{$active_project->getEditIconUrl()}" enctype="multipart/form-data" class="uniForm">
    <div class="blockLabels">
      {if !str_ends_with($active_project->getIconUrl(true), 'default.40x40.gif')}
        <div class="current_avatar">
          <div id="updated_icon">
            <img src="{$active_project->getIconUrl(true)}" alt=""/>
          </div>
          {if is_file($active_project->getIconPath(true))}
            {assign_var name=request_type}{if $request->isAsyncCall()}get{else}post{/if}{/assign_var}
            <p class="details">{link href=$active_project->getDeleteIconUrl() class='delete_current' method=$request_type}Delete Current Icon{/link}</p>
          {else}
            <p class="details">{lang}Icon inherited from client{/lang}: {company_link company=$active_project->getCompany()}.</p>
          {/if}
        </div>
      {/if}
      
      {wrap field=icon}
        {label for=projectIcon}New Icon:{/label}
        {file_field name=icon id=projectIcon}
      {/wrap}
      
      {wrap_buttons}
      	{submit}Submit{/submit}
      {/wrap_buttons}
      <input type="hidden" style="display: none;" value="submitted" name="submitted"/>
    </div>
  </form>
</div>