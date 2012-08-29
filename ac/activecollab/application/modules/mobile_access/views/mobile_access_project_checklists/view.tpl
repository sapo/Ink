<div class="wrapper">
  <div class="box">
    <div class="object_main_info">
       <h1 class="object_name">{$active_checklist->getName()|clean}</h1>
    </div>
    {mobile_access_object_properties object=$active_checklist show_completed_status=true show_tags=true show_body=true show_milestone=true}
  </div>
  {mobile_access_object_tasks object=$active_checklist}
</div>