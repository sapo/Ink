{title}Documents{/title}
{add_bread_crumb}List{/add_bread_crumb}

<div>
  <div class="object_list">
{if is_foreachable($documents)}
  
  {if $pagination->getLastPage() > 1}
	  <p class="pagination top">
	    <span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=documents page='-PAGE-'}{/pagination}</span>
		</p>
  	<div class="clear"></div>
  {/if}
  
  <table class="documents_table">
  {foreach from=$documents item=document}
    <tr class="{cycle values='odd,even'}">
      <td class="pin">
      {if $document->getIsPinned() == 0}
	      {if $document->canPinUnpin($logged_user)}
		      <a href="{$document->getPinUrl()}" class="not_pinned"><img src="{image_url name=icons/not-pinned.16x16.gif}" title="{lang}Not pinned. Click to pin to top{/lang}" alt="" /></a>
		    {else}
		      <img src="{image_url name=icons/not-pinned.16x16.gif}" title="{lang}Not pinned{/lang}" alt="" />
				{/if}
			{else}
				{if $document->canPinUnpin($logged_user)}
		      <a href="{$document->getUnpinUrl()}" class="pinned"><img src="{image_url name=icons/pinned.16x16.gif}" title="{lang}Pinned. Click to unpin{/lang}" alt="" /></a>
				{else}
		      <img src="{image_url name=icons/pinned.16x16.gif}" title="{lang}Pinned{/lang}" alt="" />
				{/if}
		  {/if}
		  </td>
      <td class="thumbnail"><a href="{$document->getViewUrl()}"><img src="{$document->getThumbnailUrl()}" alt="$document->getName()|clean"/></a></td>
      <td class="name">
      {if $document->getType() == 'text'}
        <a href="{$document->getViewUrl()}">{$document->getName()|clean}</a>
        <span class="details block">{action_on_by user=$document->getCreatedBy() datetime=$document->getCreatedOn() format=date action='Added on'}{if $active_document_category->isNew()} {lang}in{/lang} {in_category_link category_id=$document->getCategoryId() user=$logged_user}{/if}</span>
      {else}
        <a href="{$document->getViewUrl()}">{$document->getName()|clean}</a>, {$document->getSize()|filesize}
        <span class="details block">{action_on_by user=$document->getCreatedBy() datetime=$document->getCreatedOn() format=date action='Uploaded on'}{if $active_document_category->isNew()} {lang}in{/lang} {in_category_link category_id=$document->getCategoryId() user=$logged_user}{/if}</span>
      {/if}
      </td>
      <td class="options">
      {if $document->canEdit($logged_user)}
        {link href=$document->getEditUrl() title=Edit}<img src="{image_url name=gray-edit.gif}" alt="edit" />{/link}
	    {/if}
	    {if $document->canDelete($logged_user)}
	      <span class="delete"><a href="{$document->getDeleteUrl()}"><img src="{image_url name=gray-delete.gif}" title="Delete" alt="delete" /></a></span>
	    {/if}
      </td>
    {if $logged_user->canSeePrivate()}
      <td class="private">
      {if $document->getVisibility() == VISIBILITY_PRIVATE}
        <img src="{image_url name=private.gif}" title="{lang company_name=$owner_company->getName()}This document is visible only to members of :company_name company{/lang}" />
      {/if}
      </td>
    {/if}
    </tr>
  {/foreach}
  </table>
  <div class="clear"></div>
  
  {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
    {if $active_document_category->isLoaded()}
      <p class="next_page"><a href="{$active_document_category->getViewUrl($pagination->getNextPage())}">{lang}Next Page{/lang}</a></p>
    {else}
      <p class="next_page"><a href="{assemble route=documents page=$pagination->getNextPage()}">{lang}Next Page{/lang}</a></p>
    {/if}
  {/if}
{elseif $add_text_url || $upload_file_url}
    <p class="empty_page">{lang add_text_url=$add_text_url upload_file_url=$upload_file_url}No documents here. Would you like to <a href=":add_text_url">create a text document</a> or <a href=":upload_file_url">upload a file</a>?{/lang}</p>
    {empty_slate name=documents module=documents}
{else}
  	<p class="empty_page">{lang}Sorry, no documents here{/lang}</p>
{/if}
	</div>

  <ul class="category_list document_category_list">
    <li {if $active_document_category->isNew()}class="selected"{/if}><a href="{assemble route=documents}"><span>{lang}All Documents{/lang}</span></a></li>
    {if is_foreachable($categories)}
		  {foreach from=$categories item=category}
      	{if $category->canView($logged_user)}
		    	<li document_category_id="{$category->getId()}" {if $active_document_category->isLoaded() && $active_document_category->getId() == $category->getId()}class="selected"{/if}><a href="{$category->getViewUrl()}"><span>{$category->getName()|clean}</span></a></li>
		    {/if}
		  {/foreach}
		{/if}
	  {if $document_categories_url}
	    <li id="manage_document_categories"><a href="{$document_categories_url}"><span>{lang}Manage Categories{/lang}</span></a></li>
	  {/if}
  </ul>
  {if $document_categories_url}
  <script type="text/javascript">
    App.system.ManageDocumentCategories.init('manage_document_categories');
  </script>
  {/if}
  
  <div class="clear"></div>
</div>