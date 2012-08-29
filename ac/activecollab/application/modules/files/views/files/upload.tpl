{title}Upload Files{/title}
{add_bread_crumb}Upload Files{/add_bread_crumb}

{form action=$upload_url method=post enctype="multipart/form-data" id=main_form}
<div class="form_left_col">
  <table class="common_table multiupload_table">
    <tr>
      <th></th>
      <th class="input">{lang}File{/lang}</th>
      <th class="description" colspan="2">{lang}Description <i>(optional)</i>{/lang}</th>
    </tr>
    
    <tr>
      <td class="number">#1</td>
      <td class="input"><input type="file" value="" name="attachment"/></td>
      <td class="description"><input type="text" name="file[body]" /></td>
      <td class="button_column"><img src="{image_url name='gray-delete.gif'}" class="button_remove" /></td>
    </tr>
    <tr>
      <td class="number">#2</td>
      <td class="input"><input type="file" value="" name="attachment"/></td>
      <td class="description"><input type="text" name="file[body]" /></td>
      <td class="button_column"><img src="{image_url name='gray-delete.gif'}" class="button_remove" /></td>
    </tr>
    <tr>
      <td class="number">#3</td>
      <td class="input"><input type="file" value="" name="attachment"/></td>
      <td class="description"><input type="text" name="file[body]" /></td>
      <td class="button_column"><img src="{image_url name='gray-delete.gif'}" class="button_remove" /></td>
    </tr>
    
  </table>
  <div class="right_buttons">
    <a href="#" class="button_add"><span>{lang}Add Another File{/lang}</span></a>
  </div>
  <div class="clear"></div>
  
  <p class="details">{lang max_size=$max_upload_size}<strong>Note</strong>: Max upload size is :max_size per file{/lang}</p>
  
  {if $active_file->isNew()}
    {wrap field=notify_users}
      {label}Notify People{/label}
      {select_assignees_inline name=notify_users project=$active_project}
    {/wrap}
  {/if}
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
  {wrap field=visibility class="ctrlHolderNoTopPadding"}
    {label for=fileVisibility}Visibility{/label}
    {select_visibility name='file[visibility]' value=$file_data.visibility id="fileVisibility" project=$active_project short_description=true}
  {/wrap}
{else}
  <input type="hidden" name="file[visibility]" value="1" id="fileVisibility_1" />
{/if}
</div>
<div class="clear"></div>
{wrap_buttons}
  <button type="button" class="button_add" id="upload_files"><span><span>{lang}Upload{/lang}</span></span></button></td>
{/wrap_buttons}
{/form}

<form id="multiupload_form" action="{$upload_single_file_url}" method="POST" enctype="multipart/form-data">
  <input id="multiupload_parent_id" name="file[parent_id]" type="hidden" />
  <input id="multiupload_milestone_id" name="file[milestone_id]" type="hidden" />
  <input id="multiupload_tags" name="file[tags]" type="hidden" />
  <input id="multiupload_visibility" name="file[visibility]" type="hidden" />
  <input id="multiupload_body" name="file[body]" type="hidden" />
  <input type="hidden" style="display: none;" value="submitted" name="submitted"/>
</form>
