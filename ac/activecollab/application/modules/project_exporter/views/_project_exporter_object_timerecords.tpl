{if is_foreachable($_project_exporter_object_timerecords)}
  <table class="common_table">
  {foreach from=$_project_exporter_object_timerecords item=_project_exporter_object_timerecord}
  <tr>
    <td class="column_date">{$_project_exporter_object_timerecord->getRecordDate()|date:0}</td>
    <td>{$_project_exporter_object_timerecord->getName()|clean}</td>
  </tr>
  {/foreach}
  <tr>
    <td class="right strong_top_border"><strong>{lang}Total{/lang}:</strong></td>
    <td class="strong_top_border"><strong>{$_project_exporter_object_timerecords_total} {lang}hours{/lang}</strong></td>
  </tr>
  </table>
{else}

{/if}