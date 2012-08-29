{mobile_access_display_filter_list objects=$categories active_object=$active_category variable_name='category_id' enable_categories=$enable_categories action=$pagination_url}

<div class="wrapper">
{if is_foreachable($tickets)}
{foreach from=$tickets item=tickets_group}
  {if is_foreachable($tickets_group.objects)}
    {if instance_of($tickets_group.milestone, 'Milestone')}
      <h2 class="label">{$tickets_group.milestone->getName()|clean}</h2>
    {else}
      <h2 class="label">{lang}Unknown Milestone{/lang}</h2>
    {/if}
    <div class="box">
      <ul class="menu">
      {foreach from=$tickets_group.objects item=ticket}
        <li>
          <a href="{mobile_access_get_view_url object=$ticket}">
            {object_priority object=$ticket}
            <strong>#{$ticket->getTicketId()}: {$ticket->getName()|clean|excerpt:25}</strong>
          </a>
        </li>

      {/foreach}
      </ul>
    </div>
  {/if}
{/foreach}  

{else}
    <div class="box">
      <ul class="menu">
        <li>{lang}No Tickets{/lang}</li>
      </ul>
    </div>
{/if}
</div>