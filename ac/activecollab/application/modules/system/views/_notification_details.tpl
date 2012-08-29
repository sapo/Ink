{if $_object}
<div style="border: 1px solid #e8e8e8;">
  <table style="width: 100%; border-collapse: collapse">
    <tr style="background: {cycle values='#e8e8e8,#fff' name=_object_details_bg}">
      <td style="width: 150px; padding: 5px">{$_object->getVerboseType(false, $_language)|clean}</td>
      <td style="padding: 5px">{object_link object=$_object}</td>
    </tr>
  {if instance_of($_object, 'Task') && instance_of($_object->getParent(), 'ProjectObject')}
    <tr style="background: {cycle values='#e8e8e8,#fff' name=_object_details_bg}">
      <td style="width: 150px; padding: 5px">{lang language=$_language}Parent{/lang}</td>
      <td style="padding: 5px">{object_link object=$_object->getParent()}</td>
    </tr>
  {/if}
    <tr style="background: {cycle values='#e8e8e8,#fff' name=_object_details_bg}">
    {if $_object->getMilestoneId() && instance_of($_object->getMilestone(), 'Milestone')}
      <td style="padding: 5px">{lang language=$_language}Project and Milestone{/lang}</td>
      <td style="padding: 5px">{project_link project=$_object->getProject()} &raquo; {object_link object=$_object->getMilestone()}</td>
    {else}
      <td style="padding: 5px">{lang language=$_language}Project{/lang}</td>
      <td style="padding: 5px">{project_link project=$_object->getProject()}</td>
    {/if}
    </tr>
  {if $_object->can_be_completed}
    <tr style="background: {cycle values='#e8e8e8,#fff' name=_object_details_bg}">
      <td style="padding: 5px">{lang language=$_language}Priority{/lang}</td>
      <td style="padding: 5px">{$_object->getFormattedPriority($_language)}</td>
    </tr>
    {if instance_of($_object->getDueOn(), 'DateValue')}
    <tr style="background: {cycle values='#e8e8e8,#fff' name=_object_details_bg}">
      <td style="padding: 5px">{lang language=$_language}Due On{/lang}</td>
      <td style="padding: 5px">{$_object->getDueOn()|date:0}</td>
    </tr>
    {/if}
    <tr style="background: {cycle values='#e8e8e8,#fff' name=_object_details_bg}">
      <td style="padding: 5px">{lang language=$_language}Assignees{/lang}</td>
      <td style="padding: 5px">{object_assignees object=$_object language=$_language}</td>
    </tr>
  {/if}
  </table>

{if trim($_object->getBody())}
  <div style="padding: 5px; border-top: 1px solid #e8e8e8">
    <p style="text-transform: uppercase">{lang language=$_language}Summary{/lang}:</p>
    {$_object->getFormattedBody()}
  </div>
{/if}

{if $_object->can_have_attachments && $_object->hasAttachments()}
  <p style="text-transform: uppercase">{lang language=$_language}Attachments{/lang}:</p>
  <ol style="padding: 0 0 0 20px">
  {foreach from=$_object->getAttachments() item=attachment}
    <li>{object_link object=$attachment} ({$attachment->getSize()|filesize}, {$attachment->getMimeType()|clean})</li>
  {/foreach}
  </ol>
{/if}
</div>
{/if}