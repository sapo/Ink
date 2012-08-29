{if is_foreachable($_mobile_access_object_tasks_active)}
  <h2 class="label">{lang}Active tasks{/lang}</h2>
  <div class="box">
    <ul class="menu">
    {foreach from=$_mobile_access_object_tasks_active item=_task}
        <li><a href="{mobile_access_get_task_toggle_url object=$_task}"><img src="{image_url name=icons/not-checked.gif}" alt="" /> {$_task->getName()|clean}</a></li>
    {/foreach}
    </ul>
  </div>
{/if}

{if is_foreachable($_mobile_access_object_tasks_completed)}
  <h2 class="label">{lang}Completed tasks{/lang}</h2>
  <div class="box">
    <ul class="menu">
    {foreach from=$_mobile_access_object_tasks_completed item=_task}
        <li><a href="{mobile_access_get_task_toggle_url object=$_task}"><img src="{image_url name=icons/checked.gif}" alt="" /> {$_task->getName()|clean}</a></li>
    {/foreach}
    </ul>
  </div>
{/if}