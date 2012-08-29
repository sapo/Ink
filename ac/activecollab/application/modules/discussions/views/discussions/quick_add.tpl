<div class="form_wrapper">
  {if isset($active_discussion) && $active_discussion->isLoaded()}
    <p class="flash" id="success"><span class="flash_inner">{lang name=$active_discussion->getName() url=$active_discussion->getViewUrl()}Discussion <a href=":url">:name</a> has been created{/lang}</span></p>
  {/if}
  
  {form method=post id=quick_add_discussion action=$quick_add_url enctype="multipart/form-data" }
    <div class="height_limited_popup">
      <div class="quick_add_columns_container">
        <div class="quick_add_left_column"><div class="quick_add_left_column_inner">
          {wrap field=name class=first_quick_add_field}
            {label for=quick_add_discussion_title required=yes}Title{/label}
            {text_field name='discussion[name]' value=$discussion_data.name id=quick_add_discussion_title class='required title'}
          {/wrap}
          
          {wrap field=body id=quick_add_discussion_comment_wrapper}
            {label for=quick_add_discussion_comment required=yes}Message{/label}
            {textarea_field name='discussion[body]' id=quick_add_discussion_comment class=required}{$discussion_data.body}{/textarea_field}
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
                  {label for=discussionMilestone}Milestone{/label}
                  {select_milestone name='discussion[milestone_id]' value=$discussion_data.milestone_id project=$active_project id=discussionMilestone}
                {/wrap}
              </div>
              {/if}
              <div class="col_wide2">
                {wrap field=parent_id}
                  {label for=discussionParent}Category{/label}
                  {select_category name='discussion[parent_id]' value=$discussion_data.parent_id id=discussionParent module=discussions controller=discussions project=$active_project user=$logged_user}
                {/wrap}
              </div>
            </div>
          </div>
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
                {label for=discussionVisibility}Visibility{/label}
                {select_visibility name=discussion[visibility] value=$discussion_data.visibility project=$active_project short_description=true}
              {/wrap}
            </div>
          </div>
          {else}
            <input type="hidden" name="discussion[visibility]" value="1" />
          {/if}
        </div>
      </div>
    </div>
      
    <div class="wizardbar">
      {submit accesskey=no class='submit'}Submit{/submit}<a href="#" class="wizzard_back">{lang}Back{/lang}</a>
    </div>
    <input type="hidden" name="discussion[project_id]" value="{if $project_id}{$project_id}{else}{$discussion_data.project_id}{/if}" />
  {/form}
</div>

<script type="text/javascript">
{literal}
  if (App.ModalDialog.isOpen) {
    App.ModalDialog.setWidth(850);
  } // if
{/literal}
</script>