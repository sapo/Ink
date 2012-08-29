{if $_show_project}
<p><span style="font-weight: bold">{lang}Project{/lang}:</span> {project_link project=$_object->getProject()}</p>
{/if}
<p>
  <span style="font-weight: bold">{lang}Action{/lang}:</span> 
  {$_object->getVerboseType()|clean} <a href="{$_object->getViewUrl()}">{$_object->getName()|clean}</a> {lang}{$_activity->getAction()}{/lang}
</p>
<p><span style="font-weight: bold">{lang}By{/lang}:</span> {user_link user=$_activity->getCreatedBy()}</p>
{if $_activity->getComment()}
<p><span style="font-weight: bold">{lang}Comment{/lang}:</span> {$_activity->getComment()|clean}</p>
{/if}