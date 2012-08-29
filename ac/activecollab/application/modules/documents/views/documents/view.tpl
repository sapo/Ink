{page_object object=$active_document user=$active_document->getCreatedBy()}
{add_bread_crumb}View{/add_bread_crumb}

<div id="document_details">
  <div class="main_object">
    <div class="body content">{$active_document->getBody()}</div>
  </div>
  
{if $active_document->canEdit($logged_user)}
	<ul class="object_options">
	{if $logged_user->isAdministrator()}
		{if $active_document->getIsPinned() == 0}
		<li>{link href=$active_document->getPinUrl() method=post title=Pin}<span>Pin</span>{/link}</li>
		{else}
		<li>{link href=$active_document->getUnpinUrl() method=post title=Unpin}<span>Unpin</span>{/link}</li>
		{/if}
	{/if}
		<li>{link href=$active_document->getEditUrl() title=Edit}<span>Edit</span>{/link}</li>
		<li>{link href=$active_document->getDeleteUrl() method=post confirm='Are you sure that you want to permanently delete this document?' title='Delete'}<span>Delete</span>{/link}</li>
	</ul>
{/if}
</div>