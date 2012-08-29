{title}Edit File Details{/title}
{add_bread_crumb}Edit{/add_bread_crumb}

{form action=$active_file->getEditUrl() method=post ask_on_leave=yes class='big_form'}
  <div class="form_left_col">
    {wrap field=name}
      {label for=fileName required=yes}Name{/label}
      {text_field name='file[name]' value=$file_data.name id=fileName class=required}
    {/wrap}
      
    {wrap field=body}
      {label for=fileBody}Description{/label}
      {editor_field name='file[body]' id=fileBody inline_attachments=$file_data.inline_attachments}{$file_data.body}{/editor_field}
    {/wrap}    
  </div>
  <div class="form_right_col">
    {wrap field=parent_id}
      {label for=fileParent}Category{/label}
      {select_category name='file[parent_id]' value=$file_data.parent_id id=fileParent module=files controller=files project=$active_project user=$logged_user}
    {/wrap}
    
  {if $logged_user->canSeeMilestones($active_project)}
    {wrap field=milestone_id}
      {label for=fileMilestone}Milestone{/label}
      {select_milestone name='file[milestone_id]' value=$file_data.milestone_id project=$active_project id=fileMilestone}
    {/wrap}
  {/if}
    
    {wrap field=tags}
      {label for=fileTags}Tags{/label}
      {select_tags name='file[tags]' value=$file_data.tags project=$active_project id=fileTags}
    {/wrap}
    
    {if $logged_user->canSeePrivate()}
      {wrap field=visibility}
        {label for=fileVisibility}Visibility{/label}
        {select_visibility name='file[visibility]' value=$file_data.visibility project=$active_project short_description=true}
      {/wrap}
    {/if}
  </div>
  
  <div class="clear"></div>
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}