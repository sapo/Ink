{title}View Mailbox{/title}
{add_bread_crumb}View{/add_bread_crumb}


<h2 class="section_name"><span class="section_name_span">
  <form method="get" action="{assemble route=incoming_mail_admin_view_mailbox mailbox_id=$active_mailbox->getId()}" class="simple_toggler">
    <input type="checkbox" name="only_problematic" value="true" {if $only_problematic}checked="checked"{/if} id="only_active_toggler" class="input_checkbox">
    <label for="only_active_toggler">{lang}Show Only Problems{/lang}</label>
  </form>
  {lang}Activity History{/lang}
</span></h2>
<div class="section_container">
{if is_foreachable($activity_history)}
  {if $pagination->getLastPage() > 1}
  <p class="pagination top">
    <span class="inner_pagination">
    {lang}Page{/lang}: {pagination pager=$pagination}{assemble route=incoming_mail_admin_view_mailbox mailbox_id=$active_mailbox->getId() page='-PAGE-' only_problematic=$only_problematic}{/pagination}
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
          <td class="icon">
            {if $activity->getState()}
              <img src="{image_url name='ok_indicator.gif'}" />
            {else}
              <img src="{image_url name='error_indicator.gif'}" />
            {/if}
          </td>
          <td class="response">{$activity->getResponse()|clean}</td>
          <td class="sender">{$activity->getSender()|clean}</td>
          <td class="subject">{$activity->getSubject()|clean|excerpt:30}</td>
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
      <p class="next_page"><a href="{assemble route=incoming_mail_admin_view_mailbox mailbox_id=$active_mailbox->getId() page=$pagination->getNextPage()}">{lang}Next Page{/lang}</a></p>
    {/if}
{else}
  <p>{lang}No activity history for this mailbox{/lang}</p>
{/if}
</div>
