  {wrap field=description}
    {label for=itemDescription required=yes}Item Description{/label}
    {text_field name='item[description]' value=$item_data.description id=itemDescription class='required validate_minlength 3'}
  {/wrap}
  
  {wrap field=unit_cost}
    {label for=itemUnitPrice required=yes}Unit Price{/label}
    {text_field name='item[unit_cost]' value=$item_data.unit_cost id=itemUnitPrice class='required validate_number'}
  {/wrap}
  
  {wrap field=quantity}
    {label for=itemQuantity required=yes}Item Quantity{/label}
    {text_field name='item[quantity]' value=$item_data.quantity id=itemQuantity class='required validate_number'}
  {/wrap}
  
  {wrap field=tax_rate_id}
    {label for=itemTaxRateId}Tax{/label}
    {select_tax_rate name='item[tax_rate_id]' value=$item_data.tax_rate_id id=itemTaxRateId optional=yes}
  {/wrap}
