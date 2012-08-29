<div class="form_wrapper quick_add_milestone">
  {if isset($active_milestone) && $active_milestone->isLoaded()}
    <p class="flash" id="success"><span class="flash_inner">{lang name=$active_milestone->getName() url=$active_milestone->getViewUrl()}Milestone <a href=":url">:name</a> has been created{/lang}</span></p>
  {/if}
  
  {form method=post id=quick_add_milestone action=$quick_add_url}
    <div class="height_limited_popup">
      <div class="quick_add_columns_container">
        <div class="quick_add_left_column"><div class="quick_add_left_column_inner">
          {wrap field=name class=first_quick_add_field}
            {label for=quick_add_milestone_name required=yes}Summary{/label}
            {text_field name='milestone[name]' value=$milestone_data.name id=quick_add_milestone_name class=required}
          {/wrap}
          
          {wrap field=date_range}
            <div class="col">
            {wrap field=start_on}
              {label for=quick_add_milestone_start_on required=yes}Start on{/label}
              {select_date name='milestone[start_on]' value=$milestone_data.start_on id=quick_add_milestone_start_on show_timezone=no class=required}
            {/wrap}
            </div>
            
            <div class="col">
            {wrap field=due_on}
              {label for=quick_add_milestone_due_on required=yes}Due on{/label}
              {select_date name='milestone[due_on]' value=$milestone_data.due_on id=quick_add_milestone_due_on show_timezone=no class=required}
            {/wrap}
            </div>
            <div class="clear"></div>
          {/wrap}
      
        </div></div>
        
        <div class="quick_add_right_column"><div class="quick_add_right_column_inner">
          {wrap field=assignees}
            {label for=ticketAssignees}Assignees{/label}
            {select_assignees_inline name='milestone[assignees]' value=$milestone_data.assignees object=$active_milestone project=$active_project choose_responsible=true id=select_asignees_popup users_per_row=1}
          {/wrap}
        </div></div>
      </div>
    </div>
      
    <div class="wizardbar">
      {submit accesskey=no class='submit'}Submit{/submit}<a href="#" class="wizzard_back">{lang}Back{/lang}</a>
    </div>
    <input type="hidden" name="milestone[project_id]" value="{if $project_id}{$project_id}{else}{$milestone_data.project_id}{/if}" />
  {/form}
</div>

<script type="text/javascript">
{literal}
  if (App.ModalDialog.isOpen) {
    App.ModalDialog.setWidth(700);
  } // if
{/literal}
</script>