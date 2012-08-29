{title}Invoice Note Templates{/title}
{add_bread_crumb}List{/add_bread_crumb}

{if is_foreachable($invoice_note_templates)}
  <div id="invoice_item_templates_list">
    <form method="POST" action="{$reorder_note_templates_url}">
      <table class="common_table">
        <tr>
          <th></th>
          <th>{lang}Tite{/lang}</th>
          <th>{lang}Content{/lang}</th>
          <th></th>
        </tr>
        {foreach from=$invoice_note_templates item=invoice_note}
          <tr class="{cycle values='odd,even'} template" id="item_template_{$invoice_note->getId()}">
            <td class="star move_handle">
              <img src="{image_url name=move.gif}" />
              <input type="hidden" name="reorder[]" value="{$invoice_note->getId()}" />
            </td>
            <td class="description">{$invoice_note->getName()|clean}</td>
            <td class="unit_cost">{$invoice_note->getContent()|excerpt:50|clean}</td>
            <td class="options">
              {link href=$invoice_note->getEditUrl() title='Edit...'}<img src='{image_url name=gray-edit.gif}' alt='' />{/link}
              {link href=$invoice_note->getDeleteUrl() title='Move to Trash' method=post}<img src='{image_url name=gray-delete.gif}' alt='' />{/link}
            </td>
          </tr>
        {/foreach}
       </table>
      <input type="hidden" name="submitted" value="submitted" />
     </form>
   </div>
{else}
  <p class="empty_page">{lang add_url=$add_note_url}No predefined notes specified yet. <a href=":add_url">Create one now</a>.{/lang}</p>
{/if}