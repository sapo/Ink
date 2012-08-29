<div id="object_main_info" class="object_info">
  <h1>{lang}Milestones{/lang}</h1>
</div>

<div class="object_info">
  {if is_foreachable($active_milestones)}
    <h3>Active Milestones</h3>
    <table cellpadding="0" cellspacing="0" class="common_table">
      <tr>
        <th></th>
        <th>Priority</th>
        <th>Name</th>
        <th>Start On</th>
        <th>DueOn</th>
        <th>Created By</th>
      </tr>
      {project_exporter_list_objects objects=$active_milestones url_prefix='./' show_created_on=false show_start_on=true show_due_on=true show_priority=true skip_table_tag=true}
    </table>
  {/if}
  {if is_foreachable($completed_milestones)}
    <h3>Completed Milestones</h3>
    <table cellpadding="0" cellspacing="0" class="common_table">
      <tr>
        <th></th>
        <th>Priority</th>
        <th>Name</th>
        <th>Start On</th>
        <th>DueOn</th>
        <th>Created By</th>
      </tr>
      {project_exporter_list_objects objects=$completed_milestones url_prefix='./' show_created_on=false show_start_on=true show_due_on=true show_priority=true skip_table_tag=true}
    </table>
  {/if}
</div>
<div class="clear"></div>