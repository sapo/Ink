  <div id="invoice_form_head">
    <div class="col_wide" id="invoice_settings">
      {wrap field=number id=invoiceNumberGenerator}
        {label for=invoiceNumber}Invoice ID{/label}
        {if $active_invoice->getStatus() == $smarty.const.INVOICE_STATUS_ISSUED}
          {text_field name=invoice[number] value=$invoice_data.number class='' id=invoiceNumber disabled=disabled}
        {else}
          <div id="autogenerateID" style="{if $invoice_data.number}display:none{/if}">
            <div class="field_wrapper">{lang}Automatically generate on issue{/lang}<a href="#">({lang}Specify{/lang})</a></div>
          </div>
          <div id="manuallyID" style="{if !$invoice_data.number}display:none{/if}">
            <div class="field_wrapper">{text_field name=invoice[number] value=$invoice_data.number class='' id=invoiceNumber}<a href="#">({lang}Generate{/lang})</a></div>
          </div>        
        {/if}
      {/wrap}
      
      {if $active_invoice->getStatus() == $smarty.const.INVOICE_STATUS_ISSUED}
        {wrap field=issued_on}
          {label for=issueFormIssuedOn required=yes}Issued On{/label}
          <div class="field_wrapper">{select_date name=invoice[issued_on] value=$invoice_data.issued_on id=issueFormIssuedOn class=required}</div>
        {/wrap}
      
        {wrap field=issued_on}
          {label for=issueFormDueOn required=yes}Due On{/label}
          <div class="field_wrapper">{select_date name=invoice[due_on] value=$invoice_data.due_on id=issueFormDueOn class=required}</div>
        {/wrap}
      {/if}

      {wrap field=currencyId}
        {label for=currencyId required=no}Currency{/label}
        <div class="field_wrapper">{select_currency name=invoice[currency_id] value=$invoice_data.currency_id class=short id=currencyId}</div>
      {/wrap}
      
      {wrap field=language}
        {label for=userLanguage}Language{/label}
        <div class="field_wrapper">{select_language name='invoice[language_id]' value=$invoice_data.language_id optional=yes id=InvoiceLanguage}</div>
      {/wrap}
    </div>    
    <div class="col_wide" id="invoice_client">
      {wrap field=company_id}
        {label for=companyId required=yes}Client{/label}
        <div class="field_wrapper">{select_company name=invoice[company_id] value=$invoice_data.company_id class=required id="companyId" can_create_new=no}</div>
      {/wrap}
      
      {wrap field=company_address}
        {label for=companyAddress required=yes}Client Address{/label}
        <div class="field_wrapper">{textarea_field name=invoice[company_address] id=companyAddress class='required long'}{$invoice_data.company_address}{/textarea_field}</div>
      {/wrap}
      
      {wrap field=project_id}
        {label for=ProjectId required=no}Project{/label}
        <div class="field_wrapper">{select_project name=invoice[project_id] value=$invoice_data.project_id user=$logged_user optional=yes class=long}</div>
      {/wrap}      
    </div>
    <div class="clear">&nbsp;</div>
  </div>


<div class="items_wrapper">
  {wrap field=items id=invoice_items}
    <table class="validate_callback validate_invoice_items">
      <tr class="header">
        <th class="num">
          <input type="hidden" name="invoice_sub_total" id="invoice_sub_total" />
          <input type="hidden" name="invoice_total" id="invoice_total" />
        </th>
        <th class="description">{lang}Description{/lang}</th>
        <th class="unit_cost">{lang}Unit Cost{/lang}</th>
        <th class="quantity">{lang}Quantity{/lang}</th>
        <th class="tax_rate">{lang}Tax{/lang}</th>
        <th class="subtotal" style="display: none">{lang}Subtotal{/lang}</th>
        <th class="total">{lang}Total{/lang}</th>
        <th class="options"></th>
      </tr>
    {if is_foreachable($invoice_data.items)}
      {foreach from=$invoice_data.items key=iteration item=invoice_item name=invoice_items}
        {include_template name=_invoice_item_row controller=invoices module=invoicing}
      {/foreach}
    {/if}
      <tr class="invoice_totals">
        <td></td>
        <td>
          <a href="#" class="button_add" id="add_new"><span>{lang}Add New Item{/lang}</span></a>
          {if is_foreachable($invoice_item_templates)}
          <span href="#" class="button_dropdown" id="add_from_template">
            <span>{lang}Add From Template{/lang}</span>
            <div class="dropdown_container">
              <ul>
                {foreach from=$invoice_item_templates item=invoice_item_template}
                  <li><a href="{$invoice_item_template->getId()}">{$invoice_item_template->getDescription()|clean}</a></li>
                {/foreach}
              </ul>
            </div>
          </span>
          {/if}
        </td>
        <td colspan="4" class="total">
        
        </td>
        <td></td>
      </tr>
      </tr>
    </table>
  {/wrap}
</div>

{if $active_invoice->isNew() && is_foreachable($invoice_data.time_record_ids)}
  {foreach from=$invoice_data.time_record_ids item=time_record_id}
  <input type="hidden" name="invoice[time_record_ids][]" value="{$time_record_id}" />
  {/foreach}
{/if}

<div class="invoice_note_wrapper">
    {if is_foreachable($invoice_notes)}
      {wrap field=predefined_note}
        {label for=note required=no}Predefined Note{/label}
        <div class="field_wrapper">
          <select name="predefined_notes" id="predefined_notes">
            {if $original_note}
            <option value='original'>-- {lang}Original Note{/lang} --</option>
            {/if}
            <option value='empty'>-- {lang}Empty Note{/lang} --</option>
            <option value='custom'>-- {lang}Custom Note{/lang} --</option>
            <option value="empty"></div>
            {foreach from=$invoice_notes item=invoice_note key=invoice_note_id}
              <option value="{$invoice_note->getId()}">{$invoice_note->getName()|clean}</option>
            {/foreach}
          </select><br />
          {textarea_field name=invoice[note]  class='long' id=invoice_note}{$invoice_data.note}{/textarea_field}
        </div>
      {/wrap}
    {else}
      {wrap field=predefined_note}
        {label for=note required=no}Invoice Note{/label}
        {textarea_field name=invoice[note]  class='long' id=invoice_note}{$invoice_data.note}{/textarea_field}
      {/wrap}
    {/if}
    
    {wrap field=comment}
      {label for=invoiceOurComment}Our Comment{/label}
      {text_field name=invoice[comment] value=$invoice_data.comment id=invoiceOurComment class='invoice_private_note'}
      <p class="details boxless">{lang}This comment is NEVER displayed to client or included in the final invoice{/lang}</p>
    {/wrap}
  <div class="clear"></div>
</div>

{if is_foreachable($tax_rates)}
<script type="text/javascript">
{foreach from=$tax_rates item=tax_rate}
  App.invoicing.InvoiceForm.register_tax_rate({$tax_rate->getId()|json}, {$tax_rate->getName()|json}, {$tax_rate->getPercentage()|json});
{/foreach}
</script>
{/if}
