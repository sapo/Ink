{title}List emails{/title}
{add_bread_crumb}List emails{/add_bread_crumb}

{if !is_error($connection)}
  <h2 class="section_name"><span class="section_name_span">{lang unread=$unread_emails total=$total_emails}Emails in mailbox (:unread unread of :total total){/lang}</span></h2>
  <div class="section_container">
  {if is_foreachable($headers)}
    <table class="common_table">
      <tr>
        <th>{lang}UID{/lang}</th>
        <th>{lang}From{/lang}</th>
        <th>{lang}Subject{/lang}</th>
        <th>{lang}Date{/lang}</th>
      </tr>
    {foreach from=$headers item=header}
      <tr>
        <td>{$header->uid|clean}</td>
        <td>{$header->from|clean}</td>
        <td>{$header->subject|clean}</td>
        <td>{$header->date|clean}</td>
      </tr>
    {/foreach}
    </table>
  {else}
    <p>{lang}No emails in mailbox{/lang}</p>
  {/if}
  </div>
{/if}