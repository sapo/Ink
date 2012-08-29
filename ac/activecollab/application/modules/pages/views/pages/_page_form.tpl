<div class="form_left_col">
{wrap field=name}
  {label for=pageName required=yes}Name{/label}
  {text_field name='page[name]' value=$page_data.name id=pageName class='title required validate_minlength 3'}
{/wrap}

{wrap field=body}
  {label for=pageContent required=yes}Content{/label}
  {editor_field name='page[body]' id=pageContent class='validate_callback tiny_value_present' inline_attachments=$page_data.inline_attachments auto_expand=no}{$page_data.body}{/editor_field}
{/wrap}

{if $active_page->isNew()}
  <div class="ctrlHolderContainer">
    <a href="#" class="ctrlHolderToggler button_add attachments">{lang}Attach Files{/lang}...</a>
    <div class="strlHolderToggled">
    {wrap field=attachments}
      {label}Attachments{/label}
      {attach_files max_files=5}
    {/wrap}
    </div>
  </div>
  
  {wrap field=notify_users}
    {label}Notify People{/label}
    {select_assignees_inline name=notify_users project=$active_project}
  {/wrap}
{else}
  {wrap field=is_minor_revision}
    <input type="checkbox" name="page[is_minor_revision]" value="1" id="pageIsMinorRevision" class="auto" {if $page_data.is_minor_revision}checked="checked"{/if} /> {label for=pageIsMinorRevision class=inline}This is just a minor revision. Don't create a new version and don't notify subscribers about it{/label}.
  {/wrap}
{/if}
</div>

<div class="form_right_col">
  {wrap field=parent_id}
    {label for=pageParent required=yes}File Under{/label}
    {select_page name='page[parent_id]' value=$page_data.parent_id project=$active_project id=pageParent skip=$active_page user=$logged_user}
  {/wrap}
  
{if $logged_user->canSeeMilestones($active_project)}
  {wrap field=milestone_id}
    {label for=pageMilestone}Milestone{/label}
    {select_milestone name='page[milestone_id]' value=$page_data.milestone_id id=pageMilestone project=$active_project}
  {/wrap}
{/if}
  
  {wrap field=tags}
    {label for=pageTags}Tags{/label}
    {select_tags name='page[tags]' value=$page_data.tags project=$active_project id=pageTags}
  {/wrap}
  
{if $logged_user->canSeePrivate()}
  {wrap field=visibility}
    {label for=pageVisibility}Visibility{/label}
    {select_visibility name=page[visibility] value=$page_data.visibility project=$active_project short_description=true}
  {/wrap}
{else}
  <input type="hidden" name="page[visibility]" value="1">
{/if}

</div>

<div class="clear"></div>