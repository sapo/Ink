  <dl class="properties">
  {if $_object_properties_show_created_by}
    <dt>{lang}Created by{/lang}:</dt>
    <dd>{project_exporter_user_name user=$_object_properties_object->getCreatedBy()}</dd>
  {/if}
  {if $_object_properties_show_created_on}
    <dt>{lang}Created on{/lang}:</dt>
    <dd>{$_object_properties_object->getCreatedOn()|date}</dd>
  {/if}  
  {if (instance_of($_object_properties_object, 'Milestone') && $_object_properties_object->isCompleted())}
    <dt>{lang}Completed by{/lang}:</dt>
    <dd>{project_exporter_user_name user=$_object_properties_object->getCompletedBy()}</dd>
    <dt>{lang}Completed on{/lang}:</dt>
    <dd>{$_object_properties_object->getCompletedOn()|date}</dd>  
  {/if}
  {if $_object_properties_show_name}
    {if (instance_of($_object_properties_object,'File'))}
    <dt>{lang}File Name{/lang}:</dt>
    {else}
    <dt>{lang}Name{/lang}:</dt>
    {/if}
    <dd>{$_object_properties_object->getName()|clean}</dd>
  {/if}
  
  {if $_object_properties_show_file_details && instance_of($_object_properties_object,'File')}
    <dt>{lang}File Details{/lang}:</dt>
    <dd>{$_object_properties_object->getSize()|filesize} ({$_object_properties_object->getMimeType()|clean})</dd>
  {/if}
  
  {if $_object_properties_show_completed_status}
      <dt>{lang}Status{/lang}:</dt>
    {if $_object_properties_object->isCompleted()}
      <dd>{$_object_properties_object->getCompletedByName()|clean} on {$_object_properties_object->getCompletedOn()|date}</dd>
    {else}
      <dd>{lang}Open{/lang}</dd>
    {/if}
  {/if}
  
  {if $_object_properties_show_category}
    {if $_object_properties_object->getParentId()}
      {assign var=parent value=$_object_properties_object->getParent()}
      {if instance_of($parent,'Page')}
        <dt>{lang}Parent{/lang}:</dt>
        <dd><a href="./page_{$parent->getid()}.html">{$parent->getName()|clean}</a></dd>
      {else}
        <dt>{lang}Category{/lang}:</dt>
        <dd><a href="{$_object_properties_category_url_prefix}category_{$parent->getid()}.html">{$parent->getName()|clean}</a></dd>      
      {/if}
    {/if}
  {/if}
  
  {if $_object_properties_show_milestone}
    {if $_object_properties_object->getMilestoneId()}
      {assign var=milestone value=$_object_properties_object->getMilestone()}
      {if instance_of($milestone, 'Milestone')}
        <dt>{lang}Milestone{/lang}:</dt>
        {if $_object_properties_show_milestone_link}
        <dd><a href="{$_object_properties_milestone_url_prefix}milestone_{$milestone->getid()}.html">{$milestone->getName()|clean}</a></dd>        
        {else}
        <dd>{$milestone->getName()|clean}</dd>
        {/if}
      {/if}
    {/if}
  {/if}
  
  {if $_object_properties_show_milestone_day_info}
    {if $_object_properties_object->isDayMilestone()}
      <dt>{lang}Due On{/lang}:</dt>
      <dd>{$_object_properties_object->getDueOn()|date:0}</dd>
    {else}
      <dt>{lang}From / To{/lang}:</dt>
      <dd>{$_object_properties_object->getStartOn()|date:0} &mdash; {$_object_properties_object->getDueOn()|date:0}</dd>
    {/if}
  {/if}

  
  {if $_object_properties_show_priority}
      <dt>{lang}Priority{/lang}:</dt>
      <dd>{$_object_properties_object->getFormattedPriority()|clean}</dd>
  {/if}
  
  {if $_object_properties_show_tags}
    {if $_object_properties_object->hasTags()}
      <dt>{lang}Tags{/lang}:</dt>
      <dd>{implode separator=", " values=$_object_properties_object->getTags()}</dd>
    {/if}
  {/if}
  
  {assign var=attachments value=$_object_properties_object->getAttachments()}
  {if is_foreachable($attachments)}
      <dt>{lang}Attachments{/lang}:</dt>
      <dd>
        {foreach from=$attachments item=attachment}
          <a href="{$_object_properties_attachments_url_prefix}{$attachment->getId()}_{$attachment->getName()}">{$attachment->getName()}</a> 
        {/foreach}
      </dd>
  {/if}
    
  {if $_object_properties_show_body}
      {if instance_of($_object_properties_object,'Discussion')}
        <dt>{lang}Discussion Body{/lang}:</dt>
      {else}
        <dt>{lang}Description{/lang}:</dt>
      {/if}  
      <dd>
      {if $_object_properties_object->getBody()}
          <div class="body content">{$_object_properties_object->getFormattedBody()}</div>
      {else}
          <div class="body content details">{lang}No description for this object{/lang}</div>
      {/if}
      </dd>
  {/if}
 </dl>