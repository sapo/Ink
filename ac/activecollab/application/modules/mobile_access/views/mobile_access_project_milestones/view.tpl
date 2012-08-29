<div class="wrapper">
  <div class="box">
    <div class="object_main_info">
       <h1 class="object_name">{$active_milestone->getName()|clean}</h1>
    </div>
    {mobile_access_object_properties object=$active_milestone show_completed_status=true show_milestone_day_info=true show_priority=true show_tags=true show_assignees=true show_body=true}
  </div>
  
  {if is_foreachable($objects) && $total_objects}
    {foreach from=$objects key=section_name item=section_objects}
      {if is_foreachable($section_objects)}
      <h2 class="label">{$section_name}</h2>
      <div class="box">
        {mobile_access_list_objects objects=$section_objects}
      </div>
      {/if}
    {/foreach}
  {/if}
  
</div>