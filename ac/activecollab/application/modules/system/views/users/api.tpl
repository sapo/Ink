{title}API Settings{/title}
{add_bread_crumb}API Settings{/add_bread_crumb}

<div id="api_settings">
  <dl class="properties_list">
    <dt>{lang}URL{/lang}</dt>
    <dd>{$api_url|clean}</dd>
    
    <dt>{lang}Key{/lang}</dt>
    <dd>{$active_user->getToken(true)|clean} &mdash; {link href=$active_user->getResetApiKeyUrl() method=post confirm='Are you sure? With new key this user will need to update all of his subscriptions, including RSS and iCalendar feeds!'}Reset Key{/link}</dd>
    
    <dt>{lang}Status{/lang}</dt>
    <dd>
    {if $api_status == API_DISABLED}
      <span class="nok">{lang}Disabled{/lang}</span>
    {elseif $api_status == API_READ_ONLY}
      <span class="ok">{lang}Enabled, read-only{/lang}</span>
    {else}
      <span class="ok">{lang}Enabled, read and write{/lang}</span>
    {/if}
    </dd>
  </dl>
  
  {empty_slate name=api module=system}
</div>