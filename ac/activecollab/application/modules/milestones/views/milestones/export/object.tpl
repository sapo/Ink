<div id="object_main_info" class="object_info">
  <h1>{lang}Milestone{/lang}: {$active_milestone->getName()|clean}</h1>
</div>

<div id="object_details" class="object_info">
  {project_exporter_object_properties object=$active_milestone show_completed_status=true show_priority=true show_tags=true show_details=true show_milestone_day_info=true show_milestone_link=$exporting_milestones show_body=true attachments_url_prefix='../uploaded_files/'}
</div>

{if $total_objects && is_foreachable($active_milestone_objects)}
  {foreach from=$active_milestone_objects key=section_name item=objects}
    {if is_foreachable($objects)}
      <div class="object_info">
        <h3>{$section_name}</h3>
        <table class="common_table">
        {foreach from=$objects item=object}
        <tr>
          <td class="column_id"><a href="../{$object->getModule()|strtolower}/{$object->getType()|strtolower}_{$object->getId()}.html">{$object->getId()}</a></td>
          <td class="column_name"><a href="../{$object->getModule()|strtolower}/{$object->getType()|strtolower}_{$object->getId()}.html">{$object->getName()|clean}</a></td>
          <td class="column_date">{$object->getCreatedOn()|date}</td>
          <td class="column_author">{$object->getCreatedByName()|clean}</td>
        </tr>
        {/foreach}
        </table>
      </div>
    {/if}
  {/foreach}
{/if}
