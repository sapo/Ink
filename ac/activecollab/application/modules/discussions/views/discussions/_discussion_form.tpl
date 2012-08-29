<div class="form_left_col">
  {wrap field=name}
    {label for=discussionSummary required=yes}Summary{/label}
    {text_field name='discussion[name]' value=$discussion_data.name id=discussionSummary class='title required validate_minlength 3'}
  {/wrap}
  
  {wrap field=body}
    {label for=discussionBody required=yes}Message{/label}
    {editor_field name='discussion[body]' id=discussionBody class="validate_callback tiny_value_present" inline_attachments=$discussion_data.inline_attachments}{$discussion_data.body}{/editor_field}
  {/wrap}
   
{if $active_discussion->isNew()}
  <div class="ctrlHolderContainer">
    <a href="#" class="ctrlHolderToggler button_add attachments">{lang}Attach Files{/lang}...</a>
    <div class="strlHolderToggled">
    {wrap field=attachments}
      {label}Attachments{/label}
      {attach_files max_files=5}
    {/wrap}
    </div>
  </div>
{/if}

{if $active_discussion->isNew()}
  {wrap field=notify_users}
    {label}Notify People{/label}
    {select_assignees_inline name=notify_users project=$active_project}
    <div class="clear"></div>
  {/wrap}
{/if}  
</div>

<div class="form_right_col">
  {wrap field=parent_id}
    {label for=discussionParent}Category{/label}
    {select_category name='discussion[parent_id]' value=$discussion_data.parent_id id=discussionParent module=discussions controller=discussions project=$active_project user=$logged_user optional=yes}
  {/wrap}
  
{if $logged_user->canSeeMilestones($active_project)}
  {wrap field=milestone_id}
    {label for=discussionMilestone}Milestone{/label}
    {select_milestone name='discussion[milestone_id]' value=$discussion_data.milestone_id project=$active_project}
  {/wrap}
{/if}
  
  {wrap field=tags}
    {label for=discussionTags}Tags{/label}
    {select_tags name='discussion[tags]' value=$discussion_data.tags project=$active_project id=discussionTags}
  {/wrap}
  
  {if $logged_user->canSeePrivate()}
    {wrap field=visibility}
      {label for=discussionVisibility}Visibility{/label}
      {select_visibility name=discussion[visibility] value=$discussion_data.visibility project=$active_project short_description=true}
    {/wrap}
  {else}
    <input type="hidden" name="discussion[visibility]" value="1" />
  {/if}  
</div>

<div class="clear"></div>