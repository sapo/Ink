<p>
  <b>{lang}Project{/lang}:</b> {project_link project=$_assignment->getProject()}<br />
  <b>{$_assignment->getVerboseType()|clean}:</b> {object_link object=$_assignment}<br />
  <b>{lang}Priority{/lang}:</b> {$_assignment->getFormattedPriority()}<br />
{if $_assignment->getDueOn()}
  <b>{lang}Due on{/lang}:</b> {$_assignment->getDueOn()|date}<br />
{/if}
  <b>{lang}Assignees{/lang}:</b> {object_assignees object=$_assignment}
</p>

{if $_assignment->getBody()}
<hr />
{$_assignment->getFormattedBody()}
{/if}