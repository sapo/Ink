{if is_foreachable($_groupped_new_objects)}
<table class="new_obejects_since_last_visit">
{foreach from=$_groupped_new_objects item=_details}
  <tr class="{cycle values='odd,even'}">
  {if is_array($_details)}
    <td class="star">{object_star object=$_details.parent user=$logged_user}</td>
    <td class="name">
      {$_details.parent->getVerboseType()|clean}: {object_link object=$_details.parent}
      <ul>
      {foreach from=$_details.objects item=_object}
        <li>{$_object->getVerboseType()|clean}: {object_link object=$_object} <span class="details">{lang}by{/lang} {user_link user=$_object->getCreatedBy()}</span></li>
      {/foreach}
      </ul>
    </td>
    <td class="project">{lang}In{/lang} {project_link project=$_details.parent->getProject()}</td>
    <td class="user">{action_by user=$_details.parent->getCreatedBy() action=Created}</td>
  {else}
    <td class="star">{object_star object=$_details user=$logged_user}</td>
    <td class="name">{$_details->getVerboseType()|clean}: {object_link object=$_details}</td>
    <td class="project">{lang}In{/lang} {project_link project=$_details->getProject()}</td>
    <td class="user">{action_by user=$_details->getCreatedBy() action=Created}</td>
  {/if}
  </tr>
{/foreach}
</table>
{/if}