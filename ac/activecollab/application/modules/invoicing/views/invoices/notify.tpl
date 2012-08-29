{title}Invoice Updated{/title}
{add_bread_crumb}Invoice updated{/add_bread_crumb}

<div id="issue_invoice">
  {form action=$notify_url method=post}    
    <p><input type="radio" name="issue[send_emails]" value="1" {if $issue_data.send_emails}checked="checked"{/if} class="inline input_radio" id="issueFormSendEmailsYes" /> {label for="issueFormSendEmailsYes" class=inline}Send email to client{/label}</p>
    <div id="select_invoice_recipients" style="display: none">
      <table>
      {foreach from=$users item=user}
        {if $user->isCompanyManager($company)}
        <tr class="{cycle values='odd,even' name=recipient_rows}">
          <td class="radio"><input type="radio" name="issue[user_id]" value="{$user->getId()}" class="auto input_radio" {if $issue_data.user_id == $user->getId()}checked="checked"{/if} /></td>
          <td class="avatar"><img src="{$user->getAvatarUrl()}" alt="" /></td>
          <td class="name">{$user->getDisplayName()|clean} <span class="details">({$user->getEmail()|clean})</span></td>
        </tr>
        {/if}
      {/foreach}
      </table>
    </div>
    <p><input type="radio" name="issue[send_emails]" value="0" {if !$issue_data.send_emails}checked="checked"{/if} class="inline input_radio" id="issueFormSendEmailsNo" /> {label for="issueFormSendEmailsNo" class=inline}Don't send email and view updated invoice{/label}</p>
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>