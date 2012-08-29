{if is_foreachable($_list_objects_objects)}
  {if !$_list_objects_skip_table_tag}
  <table class="common_table" id="{$_list_objects_id}">
  {/if}
  {foreach from=$_list_objects_objects item=_list_objects_object}
      <tr class="{cycle values='odd,even'}">
        <td class="column_id"><a href="{$_list_objects_url_prefix}{$_list_objects_object->getType()|strtolower}_{$_list_objects_object->getId()}.html">{$_list_objects_object->getId()}</a></td>
      {if $_list_objects_show_priority}
        <td class="column_priority">{$_list_objects_object->getFormattedPriority()|clean}</td>
      {/if}
        <td class="column_name"><a href="{$_list_objects_url_prefix}{$_list_objects_object->getType()|strtolower}_{$_list_objects_object->getId()}.html">{$_list_objects_object->getName()|clean}</a></td>
        {if $_list_objects_show_created_on}
        <td class="column_date">{$_list_objects_object->getCreatedOn()|date}</td>
        {/if}
        {if $_list_objects_show_start_on}
        <td class="column_date">{$_list_objects_object->getStartOn()|date}</td>
        {/if}
        {if $_list_objects_show_due_on}
        <td class="column_date">{$_list_objects_object->getDueOn()|date}</td>
        {/if}
        
        <td class="column_author">{project_exporter_user_name user=$_list_objects_object->getCreatedBy()}</td>
      </tr>
  {/foreach}
  {if !$_list_objects_skip_table_tag}
  </table>
  {/if}
{/if}