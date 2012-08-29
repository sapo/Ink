{if is_foreachable($checklists)}
  <ul class="list_with_icons">
  {foreach from=$checklists item=checklist}
    <li class="obj_link discussions_obj_link">
      <a href="{mobile_access_get_view_url object=$checklist}">
        <span class="main_line main_line_smaller">
          {object_priority object=$checklist}
          {$checklist->getName()|clean|excerpt:18}
        </span>
        <span class="details">{lang total=$checklist->countTasks() open=$checklist->countOpenTasks()}:open open tasks of :total tasks in the list{/lang}</span>
        </a>
    </li>
  {/foreach}
  </ul>
{else}
  <div class="wrapper">
    <div class="box">
      <ul class="menu">
        <li>{lang}No Checklists{/lang}</li>
      </ul>
    </div>
  </div>
{/if}