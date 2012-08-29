{title}Upload New File{/title}
{add_bread_crumb}Upload file{/add_bread_crumb}

{form action=$upload_file_url method=post enctype="multipart/form-data" id=main_form}
<div class="form_left_col">
  {wrap field=file}
    {label for=uploadDocument required=yes}File{/label}
    <input type="file" value="" name="file"/>
    <p class="details">{lang max_size=$max_upload_size}<strong>Note</strong>: Largest file you can upload must be smaller than :max_size{/lang}</p>
  {/wrap}
  
{if $logged_user->canSeePrivate()}
  {assign_var name=normal_caption}{lang}Normal &mdash; <span class="details">Visible to anyone who has access to Documents section</span>{/lang}{/assign_var}

  {wrap field=visibility}
	  {label for=fileVisibility}Visibility{/label}
	  {select_visibility name=file[visibility] value=$file_data.visibility normal_caption=$normal_caption}
  {/wrap}
{else}
  <input type="hidden" name="file[visibility]" value="1" />
{/if}
</div>

<div class="form_right_col">
	{wrap field=category_id}
		{label for=fileCategory}Category{/label}
		{select_document_category id=fileCategory name=file[category_id] value=$file_data.category_id can_see_private=$active_document->canView($logged_user) user=$logged_user}
	{/wrap}
	
	{if $active_document->isNew()}
    {wrap field=notify_users}
      {label}Notify People{/label}
      {select_users name=notify_users}
      <div class="clear"></div>
    {/wrap}
  {/if}
</div>

<div class="clear"></div>

{wrap_buttons}
  {submit}Upload{/submit}
{/wrap_buttons}
{/form}