<div class="form_wrapper">
  {if isset($active_page) && $active_page->isLoaded()}
    <p class="flash" id="success"><span class="flash_inner">{lang name=$active_page->getName() url=$active_page->getViewUrl()}Page <a href=":url">:name</a> has been created{/lang}</span></p>
  {/if}
  
  {form method=post id=quick_add_page action=$quick_add_url enctype="multipart/form-data" }
    <div class="height_limited_popup">
      <div class="quick_add_columns_container">
        <div class="quick_add_left_column"><div class="quick_add_left_column_inner">
          <div class="col_wide">    
          {wrap field=name class=first_quick_add_field}
            {label for=quick_add_page_name required=yes}Name{/label}
            {text_field name='page[name]' value=$page_data.name id=quick_add_page_name class=required}
          {/wrap}
          </div>
          
          <div class="col_wide2">
            {wrap field=parent_id}
              {label for=pageParent required=yes}File Under{/label}
              {select_page name='page[parent_id]' value=$page_data.parent_id project=$active_project id=pageParent user=$logged_user}
            {/wrap}
          </div>   
               
          <div class="clear"></div>
          
          {wrap field=comment}
            {label for=quick_add_page_content required=yes}Content{/label}
            {textarea_field name='page[body]' id=quick_add_page_content class=required}{$page_data.body}{/textarea_field}
          {/wrap}

          {if $logged_user->canSeeMilestones($active_project)}
          <div class="ctrlHolderContainer">
              <a href="#" class="ctrlHolderToggler">{lang}Set Milestone{/lang}...</a>
            <div class="strlHolderToggled">
              <div class="col_wide">
                {wrap field=milestone_id}
                  {label for=pageMilestone}Milestone{/label}
                  {select_milestone name='page[milestone_id]' value=$page_data.milestone_id project=$active_project id=pageMilestone}
                {/wrap}
              </div>
            </div>
          </div>
          {/if}
          <div class="ctrlHolderContainer">
            <a href="#" class="ctrlHolderToggler">{lang}Attach Files{/lang}...</a>
            <div class="strlHolderToggled">
              {wrap field=popup_attachments}
                {label for=popup_attachments}Attachments{/label}
                {attach_files id=attach_files_popup max_files=5}
              {/wrap}
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
                {label for=pageVisibility}Visibility{/label}
                {select_visibility name=page[visibility] value=$page_data.visibility project=$active_project short_description=true}
              {/wrap}
            </div>
          </div>
        {else}
          <input type="hidden" name="page[visibility]" value="1" />
        {/if}

        </div>
      </div>
    </div>
    
    <div class="wizardbar">
      {submit accesskey=no class='submit'}Submit{/submit}<a href="#" class="wizzard_back">{lang}Back{/lang}</a>
    </div>
    <input type="hidden" name="page[project_id]" value="{if $project_id}{$project_id}{else}{$page_data.project_id}{/if}" />
  {/form}
</div>

<script type="text/javascript">
{literal}
  if (App.ModalDialog.isOpen) {
    App.ModalDialog.setWidth(850);
  } // if
{/literal}
</script>