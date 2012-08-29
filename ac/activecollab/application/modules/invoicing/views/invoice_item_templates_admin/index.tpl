{title}Item Templates{/title}
{add_bread_crumb}View All{/add_bread_crumb}

{if is_foreachable($invoice_item_templates)}
  <div id="invoice_item_templates_list">
    <form method="POST" action="{$reorder_item_templates_url}">
      <table class="common_table">
        <tr>
          <th></th>
          <th>{lang}Description{/lang}</th>
          <th>{lang}Unit Cost{/lang}</th>
          <th>{lang}Quantity{/lang}</th>
          <th>{lang}Tax{/lang}</th>
          <th></th>
        </tr>
        {foreach from=$invoice_item_templates item=predefined_item}
          <tr class="{cycle values='odd,even'} template" id="item_template_{$predefined_item->getId()}">
            <td class="star move_handle">
              <img src="{image_url name=move.gif}" />
              <input type="hidden" name="reorder[]" value="{$predefined_item->getId()}" />
            </td>
            <td class="description">{$predefined_item->getDescription()|clean}</td>
            <td class="unit_cost">{$predefined_item->getUnitCost()|number:2|clean}</td>
            <td class="quantity">{$predefined_item->getQuantity()|number:2|clean}</td>       
            <td class="tax">
              {assign var=tax_rate value=$predefined_item->getTaxRate()}
              {if instance_of($tax_rate, 'TaxRate')}
                {$tax_rate->getName()|clean} ({$tax_rate->getPercentage()}%)
              {/if}
            </td>
            <td class="options">
              {link href=$predefined_item->getEditUrl() title='Edit...'}<img src='{image_url name=gray-edit.gif}' alt='' />{/link}
              {link href=$predefined_item->getDeleteUrl() title='Move to Trash' method=post}<img src='{image_url name=gray-delete.gif}' alt='' />{/link}
            </td>
          </tr>
        {/foreach}
       </table>
      <input type="hidden" name="submitted" value="submitted" />
     </form>
   </div>
{else}
  <p class="empty_page">{lang add_url=$add_template_url}No predefined items specified yet. <a href=":add_url">Create one now</a>.{/lang}</p>
{/if}