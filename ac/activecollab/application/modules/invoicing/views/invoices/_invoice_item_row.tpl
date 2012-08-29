    <tr class="item {cycle values='odd,even'}" id="items_row_{$iteration}">
      <td class="num"><span>#{counter name=invoice_items}</span><img src="{image_url name=move.gif}" class="move_handle" /></td>
      <td class="description"><input type="text" name="invoice[items][{$iteration}][description]" value="{$invoice_item.description|clean}" /></td>
      <td class="unit_cost"><input type="text" name="invoice[items][{$iteration}][unit_cost]" class="short" value="{$invoice_item.unit_cost|number:2}" /></td>
      <td class="quantity"><input type="text" name="invoice[items][{$iteration}][quantity]" class="short" value="{$invoice_item.quantity|number:2}" /></td>
      <td class="tax_rate"><input type="hidden" name="invoice[items][{$iteration}][tax_rate_id]" value="{$invoice_item.tax_rate_id}" /></td>
      <td class="subtotal" style="display: none"><input type="hidden" name="invoice[items][{$iteration}][subtotal]" value="{$invoice_item.subtotal|number:2}" /></td>
      <td class="total"><input type="text" name="invoice[items][{$iteration}][total]" value="{$invoice_item.total|number:2}" /></td>
      <td class="options">
        <img src="{image_url name='gray-delete.gif'}" class="button_remove" />
        {if is_foreachable($invoice_item.time_record_ids)}
          {foreach from=$invoice_item.time_record_ids item=time_record}
            <input type="hidden" name="invoice[items][{$iteration}][time_record_ids][]" value="{$time_record}" />
          {/foreach}
        {/if}
      </td>
    </tr>