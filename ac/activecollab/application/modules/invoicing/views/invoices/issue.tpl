{title}Issue Invoice{/title}
{add_bread_crumb}Issue{/add_bread_crumb}

<div id="issue_invoice">
  {form action=$active_invoice->getIssueUrl() method=post}
    <div class="col">
      {wrap field=issued_on}
        {label for=issueFormIssuedOn required=yes}Issued On{/label}
        {select_date name=issue[issued_on] value=$issue_data.issued_on id=issueFormIssuedOn class=required}
      {/wrap}
    </div>
    
    <div class="col">
      {wrap field=issued_on}
        {label for=issueFormDueOn required=yes}Due On{/label}
        {select_date name=issue[due_on] value=$issue_data.due_on id=issueFormDueOn class=required}
      {/wrap}
    </div>
    
    <div class="clear"></div>
    
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
    <p><input type="radio" name="issue[send_emails]" value="0" {if !$issue_data.send_emails}checked="checked"{/if} class="inline input_radio" id="issueFormSendEmailsNo" /> {label for="issueFormSendEmailsNo" class=inline}Don't send emails, but mark invoice as issued{/label}</p>
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
  
  {empty_slate name=issue module=invoicing}
</div>