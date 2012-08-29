{assign_var name=_object_details_block}
{if !$_mobile_access_object_properties_only_show_body}
  {if $_mobile_access_object_properties_show_name}
    {if (instance_of($_mobile_access_object_properties_object,'File'))}
    <dt>{lang}File Name{/lang}</dt>
    {else}
    <dt>{lang}Name{/lang}</dt>
    {/if}
    <dd>{$_mobile_access_object_properties_object->getName()|clean}</dd>
  {/if}
  
  {if $_mobile_access_object_properties_show_file_details && instance_of($_mobile_access_object_properties_object,'File')}
    <dt>{lang}File Details{/lang}</dt>
    <dd>{$_mobile_access_object_properties_object->getSize()|filesize} ({$_mobile_access_object_properties_object->getMimeType()|clean})</dd>
  {/if}
  
  {if $_mobile_access_object_properties_show_completed_status}
      <dt>{lang}Status{/lang}</dt>
    {if $_mobile_access_object_properties_object->isCompleted()}
      <dd>{$_mobile_access_object_properties_object->getCompletedByName()|clean} on {$_mobile_access_object_properties_object->getCompletedOn()|date}</dd>
    {else}
      <dd>{lang}Open{/lang}</dd>
    {/if}
  {/if}
  
  {if $_mobile_access_object_properties_show_category}
    {if $_mobile_access_object_properties_object->getParentId()}
      {assign var=category value=$_mobile_access_object_properties_object->getParent()}
      <dt>{lang}Category{/lang}</dt>
      <dd><a href="{mobile_access_get_view_url object=$category}">{$category->getName()}</a></dd>
    {/if}
  {/if}
  
  {if $_mobile_access_object_properties_show_milestone}
    {if $_mobile_access_object_properties_object->getMilestoneId()}
      {assign var=milestone value=$_mobile_access_object_properties_object->getMilestone()}
      <dt>{lang}Milestone{/lang}</dt>
      <dd><a href="{mobile_access_get_view_url object=$milestone}">{$milestone->getName()}</a></dd>
    {/if}
  {/if}
  
  {if $_mobile_access_object_properties_show_total_time}
      <dt>{lang}Time{/lang}</dt>
      <dd>{$_mobile_access_object_properties_total_time} {lang}hours{/lang}</dd>
  {/if}
  
  {if $_mobile_access_object_properties_show_milestone_day_info}
    {if instance_of($_mobile_access_object_properties_object,'Milestone')}
      {if $_mobile_access_object_properties_object->isDayMilestone()}
        <dt>{lang}Due On{/lang}</dt>
        <dd>{$_mobile_access_object_properties_object->getDueOn()|date:0}</dd>
      {else}
        <dt>{lang}From / To{/lang}</dt>
        <dd>{$_mobile_access_object_properties_object->getStartOn()|date:0} &mdash; {$_mobile_access_object_properties_object->getDueOn()|date:0}</dd>
      {/if}
    {elseif instance_of($_mobile_access_object_properties_object,'Ticket')}
        <dt>{lang}Due On{/lang}</dt>
        <dd>{$_mobile_access_object_properties_object->getDueOn()|date:0}</dd>   
    {/if}
  {/if}

  
  {if $_mobile_access_object_properties_show_priority}
      <dt>{lang}Priority{/lang}</dt>
      <dd>{$_mobile_access_object_properties_object->getFormattedPriority()|clean}</dd>
  {/if}
  
  {if $_mobile_access_object_properties_show_assignees}
      <dt>{lang}Assignees{/lang}</dt>
      <dd>{mobile_access_object_assignees object=$_mobile_access_object_properties_object}</dd>
  {/if}
  
  {if $_mobile_access_object_properties_show_tags}
    {if $_mobile_access_object_properties_object->hasTags()}
      <dt>{lang}Tags{/lang}</dt>
      <dd>{implode separator=", " values=$_mobile_access_object_properties_object->getTags()}</dd>
    {/if}
  {/if}
{/if}
{/assign_var}
{if trim($_object_details_block)}
  <dl class="object_details">
    {$_object_details_block}
  </dl>
{/if}
    
  {if $_mobile_access_object_properties_show_body || $_mobile_access_object_properties_only_show_body}
    {if $_mobile_access_object_properties_object->getBody()}
      <div class="object_details">{$_mobile_access_object_properties_object->getFormattedBody()}</div>
    {else}
      <div class="object_details">{lang}No notes for this object{/lang}</div>
    {/if}
  {/if}