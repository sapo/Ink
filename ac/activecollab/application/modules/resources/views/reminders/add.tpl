{form action=$parent->getSendReminderUrl() method=post class=focusFirstField id=send_reminder_users_form}

  {if $parent->can_have_assignees}
  <div>
    <div class="label_wrapper">
      <input type="radio" name="reminder[who]" value="assignees" id="reminderAssigneesRadio" class="auto input_radio" {if $reminder_data.who == 'assignees'}checked="checked"{/if} /> {label for=reminderAssigneesRadio class=inline}Assignees{/label}
    </div>
    <div class="content_wrapper" style="display: none">
      <span class="details">
    {if is_foreachable($assignees)}
      {foreach from=$assignees item=assignee name=reminder_assignees}
        {$assignee->getDisplayName()|clean}{if !$smarty.foreach.reminder_assignees.last}, {/if}
      {/foreach}
    {else}
        {lang}There are no assignees{/lang}
    {/if}
      </span>
    </div>
  </div>
  {/if}
  
  {if $parent->can_have_subscribers}
  <div>
    <div class="label_wrapper">
      <input type="radio" name="reminder[who]" value="subscribers" id="reminderSubscribersRadio" class="auto input_radio" {if $reminder_data.who == 'subscribers'}checked="checked"{/if} /> {label for=reminderSubscribersRadio class=inline}Subscribers{/label}
    </div>
    <div class="content_wrapper" style="display: none">
      <span class="details">
    {if is_foreachable($subscribers)}
      {foreach from=$subscribers item=subscriber name=reminder_subscribers}
        {$subscriber->getDisplayName()|clean}{if !$smarty.foreach.reminder_subscribers.last}, {/if}
      {/foreach}
    {else}
        {lang}There are no users subscribed to this object{/lang}
    {/if}
      </span>
    </div>
  </div>
  {/if}
  
  {if $parent->can_have_comments}
  <div>
    <div class="label_wrapper">
      <input type="radio" name="reminder[who]" value="commenters" id="reminderCommentersRadio" class="auto input_radio" {if $reminder_data.who == 'commenters'}checked="checked"{/if} /> {label for=reminderCommentersRadio class=inline}Everyone involved in a discussion{/label}
    </div>
    <div class="content_wrapper" style="display: none">
      <span class="details">
    {if is_foreachable($commenters)}
      {foreach from=$commenters item=commenter name=reminder_commenters}
        {$commenter->getDisplayName()|clean}{if !$smarty.foreach.reminder_commenters.last}, {/if}
      {/foreach}
    {else}
        {lang}No users involved in a discussion{/lang}
    {/if}
      </span>
    </div>
  </div>
  {/if}
  
  <div>
    <div class="label_wrapper">
      <input type="radio" name="reminder[who]" value="user" id="reminderUserRadio" class="auto input_radio" {if $reminder_data.who == 'user'}checked="checked"{/if} /> {label for=reminderUserRadio class=inline}Selected User{/label}
    </div>
    <div class="content_wrapper" style="display: none">
      {select_user name='reminder[user_id]' users=$project_users optional=no}
    </div>
  </div>
  
  {wrap field=comment}
    {label for=reminderComment}Optional Comment{/label}
    {textarea_field name='reminder[comment]'}{$reminder_data.comment}{/textarea_field}
    <p class="details boxless">{lang}HTML not supported! Line breaks are preserved. Links are recognized and converted{/lang}.</p>
  {/wrap}
  
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}

<script type="text/javascript">
  App.widgets.SendReminder.init_form();
</script>