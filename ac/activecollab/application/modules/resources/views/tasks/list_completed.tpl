      {foreach from=$completed_tasks item=_object_task}
        {include_template module=resources controller=tasks name=_task_completed_row}
      {/foreach}