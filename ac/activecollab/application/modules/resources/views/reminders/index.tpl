{title}Active Reminders{/title}
{add_bread_crumb}Active Reminders{/add_bread_crumb}

{if is_foreachable($active_reminders)}
  <div id="active_reminders" class="height_limited_popup">
    <table class="common_table reminders_table">
      <tr>
        <th>{lang}Reminder{/lang}</th>
        <th>{lang}From{/lang}</th>
        <th></th>
      </tr>
    {foreach from=$active_reminders item=active_reminder}
      <tr>
        <td class="reminder">
          {if $active_reminder->getObjectId()}
            <strong>{object_link object=$active_reminder->getObject()}</strong><br />
          {/if}
          {if $active_reminder->getComment()}
            {$active_reminder->getComment()}
          {/if}
        </td>
        <td class="from">{user_link user=$active_reminder->getCreatedBy()}</td>
        <td class="options"><a href="{$active_reminder->getDismissUrl()}"><img src='{image_url name=gray-delete.gif}' alt='' /></a></td>
      </tr>
    {/foreach}
    </table>
  </div>
  <script type="text/javascript">
    App.widgets.ActiveReminders.init('active_reminders');
  </script>
{/if}

<p class="empty_page" style="{if is_foreachable($active_reminders)}display: none{/if}">{lang}There are no reminders for you{/lang}</p>