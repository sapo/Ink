{if $active_category->isLoaded()}
  {title not_lang=yes}{$active_category->getName()}{/title}
{else}
  {title}Files{/title}
{/if}
{add_bread_crumb}{lang page=$pagination->getCurrentPage()}Page :page{/lang}{/add_bread_crumb}

<div class="list_view" id="files">
  <div class="object_list">
  {if is_foreachable($files)}
    {if $pagination->getLastPage() > 1}
      <p class="pagination top">
      {if isset($active_category) && instance_of($active_category, 'Category') && $active_category->isLoaded()}
        <span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=project_files project_id=$active_project->getId() page='-PAGE-' category_id=$active_category->getId()}{/pagination}</span>
      {else}
        <span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=project_files project_id=$active_project->getId() page='-PAGE-'}{/pagination}</span>
      {/if}
      </p>
      <div class="clear"></div>
    {/if}
  
    {form method="POST" action=$mass_edit_url}
      <input type="hidden" name="object_types" value="files" />
      <table id="file_list" class="common_table">
        <tr>
          <th></th>
          <th>{lang}Thumbnail{/lang}</th>
          <th>{lang}File Details{/lang}</th>
          <th></th>
          <th></th>
          <th class="checkbox"><input type="checkbox" class="auto master_checkbox input_checkbox" /></th>
        </tr>
        <tbody>
      {foreach from=$files item=file}
        {if instance_of($file, 'File')}
          <tr class="file {cycle values='odd,even'}">
            <td class="star"><span class="star">{object_star object=$file user=$logged_user}</span></td>
            <td class="thumbnail"><a href="{$file->getViewUrl()}"><img src="{$file->getThumbnailUrl()}" alt="{lang}Thumbnail{/lang}" /></a></td>
            <td class="details">
              <dl>
                <dt>{lang}File{/lang}</dt>
                <dd class="filename"><a href="{$file->getViewUrl()}" title="{$file->getName()|clean}">{$file->getName()|excerpt:40|clean}</a>, {$file->getSize()|filesize}</dd>
                
              {if $file->getBody()}
                <dt>{lang}Description{/lang}</dt>
                <dd class="description">{$file->getBody()|excerpt:100:'...':true|clean}</dd>
              {/if}
                
              {if $file->getRevision() == 1}
                <dt>&nbsp;</dt>
                <dd>{action_on_by user=$file->getCreatedBy() datetime=$file->getCreatedOn() action='Uploaded'}</dd>
              {else}
                <dt>{lang}Latest Version{/lang}</dt>
                <dd>#{$file->getRevision()} &mdash; {action_on_by user=$file->getLastRevisionBy() datetime=$file->getLastRevisionOn() action='Uploaded'}</dd>
                
                <dt>{lang}First Version{/lang}</dt>
                <dd>{action_on_by user=$file->getCreatedBy() datetime=$file->getCreatedOn() action='Uploaded'}</dd>
              {/if}
              </dl>
            </td>
            <td class="options">
              <a href="{$file->getDownloadUrl(true)}" class="button_add">{lang}Download{/lang}</a>
              {if $file->canEdit($logged_user)}
              <br /><a href="{$file->getNewVersionUrl()}">{lang}New Version{/lang}</a>
              {/if}
            </td>
            <td class="visibility">
              {if $logged_user->canSeePrivate() && ($file->getVisibility() <= VISIBILITY_PRIVATE)}
                {object_visibility object=$file user=$logged_user}
              {/if}
            </td>
            <td class="checkbox">
              {if $file->canDelete($logged_user)}
                <input type="checkbox" name="files[]" value="{$file->getId()}" class="auto slave_checkbox input_checkbox" />
              {/if}
            </td>
          </tr>
        {/if}
      {/foreach}
        </tbody>
      </table>
      
      <!-- MASS EDIT START -->
      <div id="mass_edit">
        <select name="with_selected" class="auto conflicts_action" id="file_list_action">
          <option value="">{lang}With selected ...{/lang}</option>
          <option value=""></option>
          <option value="move_to_trash">{lang}Move To Trash{/lang}</option>
          {if is_foreachable($categories)}
          <option value=""></option>
          <optgroup label="{lang}Move To Category{/lang}">
            <option value="move_to_category">{lang}&lt;None&gt;{/lang}</option>
            {foreach from=$categories item=category}
            <option value="move_to_category_{$category->getId()}">{$category->getName()|clean}</option>
            {/foreach}
          </optgroup>
          {/if}
        </select>
        <button class="simple" id="file_list_submit" type="submit" class="auto conflicts_submit">{lang}Go{/lang}</button>
      </div>
      <!-- MASS EDIT END -->
    {/form}
    <div class="clear"></div>
    
    
    <!-- PAGINATION START -->
    {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
      {if isset($active_category) && instance_of($active_category, 'Category') && $active_category->isLoaded()}
        <p class="next_page"><a href="{assemble route=project_files project_id=$active_project->getId() page=$pagination->getNextPage() category_id=$active_category->getId()}">{lang}Next Page{/lang}</a></p>
      {else}
        <p class="next_page"><a href="{assemble route=project_files project_id=$active_project->getId() page=$pagination->getNextPage()}">{lang}Next Page{/lang}</a></p>
      {/if}
    {/if}
    <!-- PAGINATION END -->
  {else}
    <!-- EMPTY PAGE START -->
    {if instance_of($active_category, 'Category') && $active_category->isLoaded()}
      <p class="empty_page">{lang}There are no files in this category{/lang}. {if $upload_url}{lang add_url=$upload_url}<a href=":add_url">Upload now</a>{/lang}?{/if}</p>
    {else}
      <p class="empty_page">{lang}There are no files to show{/lang}. {if $upload_url}{lang add_url=$upload_url}<a href=":add_url">Upload now</a>{/lang}?{/if}</p>
      {empty_slate name=files module=files}
    {/if}
      <!-- EMPTY PAGE END -->
  {/if}
  </div>
  
  <!-- CATEGORY LIST START-->
  <ul class="category_list">
    <li {if ($active_category->isNew() && !$attachments_view)}class="selected"{/if}><a href="{$files_url}"><span>{lang}All Files{/lang}</span></a></li>
    <li {if $attachments_view}class="selected"{/if}><a href="{$attachments_url}"><span>{lang}All Attachments{/lang}</span></a></li>
    {if is_foreachable($categories)}
      {foreach from=$categories item=category}
      <li category_id="{$category->getId()}" {if $active_category->isLoaded() && $active_category->getId() == $category->getId()}class="selected"{/if}><a href="{assemble route=project_files project_id=$active_project->getId() category_id=$category->getId()}"><span>{$category->getName()|clean}</span></a></li>
      {/foreach}
    {/if}
    {if $can_manage_categories}
      <li id="manage_categories"><a href="{$categories_url}"><span>{lang}Manage Categories{/lang}</span></a></li>
    {/if}
  </ul>
  <script type="text/javascript">
    App.resources.ManageCategories.init('manage_categories');
  </script>
  <!-- CATEGORY LIST END -->
  
  <div class="clear"></div>
</div>