{if is_foreachable($timerecords)}
  <table class="timerecords_table">
    <tr>
      <th>{lang}Date{/lang}</th>
      <th>{lang}Details{/lang}</th>
      <th>{lang}H{/lang}</th>
      <th>{lang}B{/lang}</th>
    </tr>
  {foreach from=$timerecords item=timerecord}
    {assign var=user value=$timerecord->getUser()}
    <tr class="time_record {cycle values='odd,even'} {if $timerecord->isBilled()}billed{/if}">
      <td class="date">{$timerecord->getRecordDate()|date:0}</td>
      <td class="user">
        <ul>
          <li>{lang}by{/lang} <a href="{mobile_access_get_view_url object=$user}">{$user->getName()|clean|excerpt:25}</a></li>
          {assign var=parent value=$timerecord->getParent()}
          {if instance_of($parent, 'ProjectObject')}
          <li>{lang}on{/lang} <a href="{mobile_access_get_view_url object=$parent}">{$parent->getName()|clean|excerpt:25}</a></li>
          {/if}
          {if $timerecord->getBody()}
          <li>{$timerecord->getBody()|clean}</li>
          {/if}
        </ul>
      </td>
      <td class="hours"><b>{$timerecord->getValue()}</b></td>
      <td class="billed">
      {if $timerecord->isBilled()}
        {lang}Yes{/lang}
      {else}
        {lang}No{/lang}
      {/if}
      </td>
    </tr>
  {/foreach}
  </table>
  {mobile_access_paginator paginator=$pagination url=$pagination_url}
{else}
  <div class="wrapper">
    <div class="box">
      <ul class="menu">
        <li>{lang}No Timerecords{/lang}</li>
      </ul>
    </div>
  </div>
{/if}