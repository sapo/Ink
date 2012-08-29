<div class="form_left_col">
  {wrap field=name}
    {label for=documentTitle required=yes}Title{/label}
    {text_field name='document[name]' value=$document_data.name id=documentTitle class='title required'}
  {/wrap}
  
  {if $active_document->isNew() || $active_document->getType() == 'text'}
	  {wrap field=body}
	    {label for=documentBody required=yes}Document{/label}
	    {editor_field name='document[body]' id=documentBody class="validate_callback tiny_value_present" inline_attachments=$document_data.inline_attachments}{$document_data.body}{/editor_field}
	  {/wrap}
  {/if}
  
  {assign_var name=normal_caption}{lang}Normal &mdash; <span class="details">Visible to everyone who has access to Documents section</span>{/lang}{/assign_var}
  {assign_var name=private_caption}{lang owner_company=$owner_company->getName()}Private &mdash; <span class="details">Visible only to members of :owner_company company</span>{/lang}{/assign_var}
  
{if $logged_user->canSeePrivate()}
  {assign_var name=normal_caption}{lang}Normal &mdash; <span class="details">Visible to anyone who has access to Documents section</span>{/lang}{/assign_var}
  
  {wrap field=visibility}
	  {label for=documentVisibility}Visibility{/label}
	  {select_visibility name=document[visibility] value=$document_data.visibility normal_caption=$normal_caption}
  {/wrap}
{else}
  <input type="hidden" name="file[visibility]" value="1" />
{/if}
</div>

<div class="form_right_col">
	{wrap field=category_id}
		{label for=documentCategory}Category{/label}
		{select_document_category name=document[category_id] value=$document_data.category_id id=documentCategory user=$logged_user}
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