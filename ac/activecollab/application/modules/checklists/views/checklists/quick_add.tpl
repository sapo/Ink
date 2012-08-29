<div class="form_wrapper">
  {if isset($active_checklist) && $active_checklist->isLoaded()}
    <p class="flash" id="success"><span class="flash_inner">{lang name=$active_checklist->getName() url=$active_checklist->getViewUrl()}Checklists <a href=":url">:name</a> has been created{/lang}</span></p>
  {/if}
  
  {form method=post id=quick_add_checklist action=$quick_add_url}
    <div class="height_limited_popup">
      <div class="quick_add_columns_container">
        <div class="quick_add_left_column"><div class="quick_add_left_column_inner">
          {wrap field=name class=first_quick_add_field}
            {label for=quick_add_checklist_name required=yes}Name{/label}
            {text_field name='checklist[name]' value=$checklist_data.name id=quick_add_checklist_name class=required}
          {/wrap}
          
          {wrap field=tasks}
            {label}Tasks{/label}
            {section name=checklist_tasks loop=5}
              <p class="quick_add_checklist_task">
              {if $checklist_data.tasks}
                <input type="text" name="checklist[tasks][{$smarty.section.checklist_tasks.index}]" value="{$checklist_data.tasks[checklist_tasks]|clean}" /></p>
              {else}
                <input type="text" name="checklist[tasks][{$smarty.section.checklist_tasks.index}]" /></p>
              {/if}
              </p>
            {/section}
          {/wrap}
          
          {if $logged_user->canSeeMilestones($active_project)}
          <div class="ctrlHolderContainer">
              <a href="#" class="ctrlHolderToggler">{lang}Set Milestone{/lang}...</a>
            <div class="strlHolderToggled">
              <div class="col_wide">
                {wrap field=milestone_id}
                  {label for=pageMilestone}Milestone{/label}
                  {select_milestone name='checklist[milestone_id]' value=$checklist_data.milestone_id project=$active_project id=pageMilestone}
                {/wrap}
              </div>
            </div>
          </div>
          {/if}
        </div></div>
        
        <div class="quick_add_right_column"><div class="quick_add_right_column_inner">
          {wrap field=assignees}
            {label for=checklistAssignees}Assignees for Tasks{/label}
            {select_assignees_inline name='checklist[assignees]' value=$checklist_data.assignees object=$active_checklist project=$active_project choose_responsible=true id=select_asignees_popup users_per_row=1}
          {/wrap}
        </div>
          {if $logged_user->canSeePrivate()}
          <div class="ctrlHolderContainer">
            <a href="#" class="ctrlHolderToggler">{lang}Set Visibility{/lang}...</a>
            <div class="strlHolderToggled">
              {wrap field=visibility}
                {label for=checklistVisibility}Visibility{/label}
                {select_visibility name=checklist[visibility] value=$checklist_data.visibility project=$active_project short_description=true}
              {/wrap}
            </div>
          </div>
          {else}
            <input type="hidden" name="checklist[visibility]" value="1" />
          {/if}
        </div>
      </div>
    </div>
      
    <div class="wizardbar">
      {submit accesskey=no class='submit'}Submit{/submit}<a href="#" class="wizzard_back">{lang}Back{/lang}</a>
    </div>
    <input type="hidden" name="checklist[project_id]" value="{if $project_id}{$project_id}{else}{$checklist_data.project_id}{/if}" />
  {/form}
</div>


<script type="text/javascript">
{literal}
  if (App.ModalDialog.isOpen) {
    App.ModalDialog.setWidth(850);
  } // if
{/literal}
</script>