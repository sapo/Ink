{title}Incoming Mail Settings{/title}
{add_bread_crumb}Settings{/add_bread_crumb}

  <h2 class="section_name"><span class="section_name_span">{lang}Defined Mailboxes{/lang}</span></h2>
  <div class="section_container">
  {if is_foreachable($mailboxes)} 
    <table class="common_table" id="mailbox_table">
      <tr>
        <th>{lang}Mailbox{/lang}</th>
        <th colspan="2">{lang}Last Status{/lang}</th>
        <th class="mailbox_active">{lang}Active{/lang}</th>
        <th>{lang}Creates{/lang}</th>
        <th>{lang}Project{/lang}</th>
        <th>{lang}Options{/lang}</th>
      </tr>
    {foreach from=$mailboxes item=mailbox}
      <tr class="{cycle values='odd,even'}" id="mailbox_{$mailbox->getId()}">
         <td class="account_email">{$mailbox->getDisplayName()|clean}</td>
         <td class="icon">
          <a href="{$mailbox->getViewUrl()}">
          {if $mailbox->getLastStatus() === 1}
            <img src="{image_url name='ok_indicator.gif'}" />
          {else}
            <img src="{image_url name='error_indicator.gif'}" />
          {/if}
          </a>
         </td>
         <td class="incoming_mailbox_status_{$mailbox->getLastStatus()}">
          <a href="{$mailbox->getViewUrl()}">{$mailbox->getFormattedLastStatus()}</a>
         </td>

         <td class="mailbox_active">
          <strong>
           {if $mailbox->getEnabled()}
             {lang}Yes{/lang}
           {else}
             {lang}No{/lang}
           {/if}
           </strong>
         </td>
         <td class="object_type">{$mailbox->getObjectType()|clean|humanize}</td>
         <td class="mailbox_host">{$mailbox->getProjectName()|clean}</td>
         <td class="options">
          {link href=$mailbox->getViewUrl() title='Activity History...'}<img src='{image_url name=arrow-right-small.gif}' alt='' />{/link} 
          {link href=$mailbox->getListEmailsUrl() title='List Messages in mailbox...'}<img src='{image_url name=info-gray.gif}' alt='' />{/link} 
          {link href=$mailbox->getEditUrl() title='Edit...'}<img src='{image_url name=gray-edit.gif}' alt='' />{/link} 
          {link href=$mailbox->getDeleteUrl() title='Delete...' method=post}<img src='{image_url name=gray-delete.gif}' alt='' />{/link} 
         </td>
      </tr>
    {/foreach}
    </table>
  {else}
      <p class="empty_page">{lang}No mailboxes defined here{/lang}. {lang add_url=$add_new_mailbox_url}Would you like to <a href=":add_url">create one</a>{/lang}?</p>
      {empty_slate name=mailboxes module=incoming_mail}
  {/if}
  </div>

{if is_foreachable($activity_history)}
  <h2 class="section_name"><span class="section_name_span">
    <form method="get" action="{assemble route=incoming_mail_admin}" class="simple_toggler">
      <input type="checkbox" name="only_problematic" value="true" {if $only_problematic}checked="checked"{/if} id="only_active_toggler" class="input_checkbox">
      <label for="only_active_toggler">{lang}Show Only Problems{/lang}</label>
    </form>
    {lang}Activity Log{/lang}
  </span></h2>
  <div class="section_container">
    {if $pagination->getLastPage() > 1}
    <p class="pagination top">
      <span class="inner_pagination">
      {lang}Page{/lang}: {pagination pager=$pagination}{assemble route=incoming_mail_admin page='-PAGE-' only_problematic=$only_problematic}{/pagination}
      </span>
    </p>
    <div class="clear"></div>
    {/if}
    
    <div id="recent_activities" class="incoming_mail_activities">
    {foreach from=$activity_history key=date item=activities}
    <h3 class="day_section">{$date|clean}</h3>
    <table class="common_table incoming_mail_log_table">
      <tbody>
      {foreach from=$activities item=activity}
        <tr class="{if $activity->getState()}incoming_mail_ok{else}incoming_mail_conflict{/if} {cycle values='odd,even'}">
          <td class="time">{$activity->getCreatedOn()|time}</td>
          <td class="mailbox_name"><a href="{$activity->getMailboxViewUrl()}">{$activity->getMailboxDisplayName()|clean}</a></td>
          <td class="icon">
            {if $activity->getState()}
              <img src="{image_url name='ok_indicator.gif'}" />
            {else}
              <img src="{image_url name='error_indicator.gif'}" />
            {/if}
          </td>
          <td class="response">{$activity->getResponse()|clean}</td>
          <td class="sender">{$activity->getSender()|clean}</td>
          <td class="subject"><span title="{$activity->getSubject()|clean}">{$activity->getSubject()|clean|excerpt:30}</span></td>
          <td class="options">
            {if $activity->getProjectObjectId() || $activity->getIncomingMailId()}
              <a href="{$activity->getResultingObjectUrl()}"><img src='{image_url name=arrow-right-small.gif}' /></a>
            {/if}
          </td>
        </tr>
      {/foreach}
      </tbody>
    </table>
    {/foreach}
    </div>
    {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
      <p class="next_page"><a href="{assemble route=incoming_mail_admin page=$pagination->getNextPage()}">{lang}Next Page{/lang}</a></p>
    {/if}
  </div>
{/if}




