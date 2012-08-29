<div id="object_main_info" class="object_info">
  <h1>{lang}Checklist{/lang}: {$object->getName()|clean}</h1>
</div>

<div id="object_details" class="object_info">
  {project_exporter_object_properties object=$object show_completed_status=true show_milestone=true milestone_url_prefix='../milestones/' show_tags=true show_body=true show_milestone_link=$exporting_milestones attachments_url_prefix='../uploaded_files/'}
</div>

{project_exporter_object_tasks object=$object}
