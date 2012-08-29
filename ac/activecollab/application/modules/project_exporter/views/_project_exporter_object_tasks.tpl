{if is_foreachable($_object_tasks_open_tasks) || is_foreachable($_object_tasks_completed_tasks)} 
  <div id="object_tasks" class="object_info">
    <h3>{lang}Tasks{/lang}</h3>
    <table id="{$id}" class="common_table">
    {if is_foreachable($_object_tasks_open_tasks)}
      {foreach from=$_object_tasks_open_tasks item=_object_tasks_open_task}
      <tr>
        <td>{$_object_tasks_open_task->getName()}</td>
        <td class="column_author">{project_exporter_user_name user=$_object_tasks_open_task->getCreatedBy()}</td>
        <td class="column_date">{$_object_tasks_open_task->getCreatedOn()|date}</td>
      </tr>
      {/foreach}
    {/if}
    {if is_foreachable($_object_tasks_completed_tasks)}
      {foreach from=$_object_tasks_completed_tasks item=_object_tasks_completed_task}
      <tr>
        <td><del>{$_object_tasks_completed_task->getName()}</del></td>
        <td class="column_author">{project_exporter_user_name user=$_object_tasks_completed_task->getCreatedBy()}</td>
        <td class="column_date">{$_object_tasks_completed_task->getCompletedOn()|date}</td>
      </tr>
      {/foreach}
    {/if}
    </table>
  </div>
{/if}