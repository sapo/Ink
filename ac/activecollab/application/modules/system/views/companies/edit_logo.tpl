{title}Update Logo{/title}
{add_bread_crumb}Update Logo{/add_bread_crumb}

<div id="edit_icon">
  <form method="POST" action="{$active_company->getEditLogoUrl()}" enctype="multipart/form-data" class="uniForm">
    <div class="blockLabels">
      {if file_exists($active_company->getLogoPath(true))}
        <div class="current_avatar">
          <div id="updated_icon">
            <img src="{$active_company->getLogoUrl(true)}" alt="" />
          </div>
          {assign_var name=request_type}{if $request->isAsyncCall()}get{else}post{/if}{/assign_var}
          <p class="details">{link href=$active_company->getDeleteLogoUrl() method=post class='delete_current' method=$request_type}Delete Current Logo{/link}</p>
        </div>
      {/if}
      
      {wrap field=logo}
        {label for=companyLogo}New Logo:{/label}
        {file_field name=logo id=companyLogo}
      {/wrap}
      
      {wrap_buttons}
      	{submit}Submit{/submit}
      {/wrap_buttons}
      <input type="hidden" style="display: none;" value="submitted" name="submitted"/>
    </div>
  </form>
</div>