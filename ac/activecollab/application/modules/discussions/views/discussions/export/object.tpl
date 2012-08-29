<div id="object_main_info" class="object_info">
  <h1>{lang}Discussion{/lang}: {$object->getName()|clean}</h1>
</div>

<div id="object_details" class="object_info">
  {project_exporter_object_properties object=$object show_category=true category_url_prefix='./' show_milestone=true milestone_url_prefix='../milestones/' show_tags=true show_milestone_link=$exporting_milestones show_body=true attachments_url_prefix='../uploaded_files/'}
</div>

{project_exporter_comments comments=$comments attachments_url_prefix='../uploaded_files/'}