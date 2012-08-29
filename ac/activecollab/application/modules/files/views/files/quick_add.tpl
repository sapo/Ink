<div class="form_wrapper">
  {if isset($active_file) && $active_file->isLoaded()}
    <p class="flash" id="success"><span class="flash_inner">{lang name=$active_file->getName() url=$active_file->getViewUrl()}File <a href=":url">:name</a> has been created{/lang}</span></p>
  {/if}
  
  {form method=post id=quick_add_file enctype="multipart/form-data" action=$quick_add_url}
    <div class="height_limited_popup">
      <div class="quick_add_columns_container">
        <div class="quick_add_left_column"><div class="quick_add_left_column_inner">
          {wrap field=attachments class=first_quick_add_field}
            {label for=quick_add_file_file required=yes}File{/label}
            {file_field name=file id=quick_add_file_file}
          {/wrap}
          {wrap field=body}
            {label for=quick_add_file_description}Description{/label}
            {textarea_field name='file[body]' id=quick_add_file_description}{$file_data.body}{/textarea_field}
          {/wrap}
          <div class="ctrlHolderContainer">
            {if $logged_user->canSeeMilestones($active_project)}
              <a href="#" class="ctrlHolderToggler">{lang}Set Milestone and Category{/lang}...</a>
            {else}
              <a href="#" class="ctrlHolderToggler">{lang}Set Category{/lang}...</a>
            {/if}
            <div class="strlHolderToggled">
              {if $logged_user->canSeeMilestones($active_project)}
              <div class="col_wide">
                {wrap field=milestone_id}
                  {label for=fileMilestone}Milestone{/label}
                  {select_milestone name='file[milestone_id]' value=$file_data.milestone_id project=$active_project id=fileMilestone}
                {/wrap}
              </div>
              {/if}
              <div class="col_wide2">
                {wrap field=parent_id}
                  {label for=fileParent}Category{/label}
                  {select_category name='file[parent_id]' value=$file_data.parent_id id=fileParent module=files controller=files project=$active_project user=$logged_user}
                {/wrap}
              </div>
            </div>
          </div>
        </div></div>
        <div class="quick_add_right_column"><div class="quick_add_right_column_inner">
          {wrap field=notify_users}
            {label}Notify People{/label}
            {select_assignees_inline name=notify_users project=$active_project id=select_asignees_popup users_per_row=1}
            <div class="clear"></div>
          {/wrap}
        </div>
            {if $logged_user->canSeePrivate()}
            <div class="ctrlHolderContainer">
              <a href="#" class="ctrlHolderToggler">{lang}Set Visibility{/lang}...</a>
              <div class="strlHolderToggled">
                {wrap field=visibility}
                  {label for=fileVisibility}Visibility{/label}
                  {select_visibility name=file[visibility] value=$file_data.visibility project=$active_project short_description=true}
                {/wrap}
              </div>
            </div>
            {else}
              <input type="hidden" name="file[visibility]" value="1" />
            {/if}
        </div>
      </div>
    </div>
        
    <div class="wizardbar">
      {submit accesskey=no class='submit'}Submit{/submit}<a href="#" class="wizzard_back">{lang}Back{/lang}</a>
    </div>
    <input type="hidden" name="file[project_id]" value="{if $project_id}{$project_id}{else}{$file_data.project_id}{/if}" />    
  {/form}
</div>

<script type="text/javascript">
{literal}
  if (App.ModalDialog.isOpen) {
    App.ModalDialog.setWidth(850);
  } // if
{/literal}
</script>