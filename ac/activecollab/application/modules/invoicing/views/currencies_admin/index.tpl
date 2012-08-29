{title}Currencies{/title}
{add_bread_crumb}List All{/add_bread_crumb}

<div id="currencies">
{if is_foreachable($currencies)}
  <table class="common_table">
    <tr>
      <th class="checkbox"></th>
      <th class="name">{lang}Currency{/lang}</th>
      <th class="code">{lang}Code{/lang}</th>
      <th class="rate">{lang}Default Rate{/lang}</th>
      <th class="options"></th>
    </tr>
  {foreach from=$currencies item=currency}
    <tr class="{cycle values='odd,even'}">
      <td class="checkbox"><input type="checkbox" class="auto input_checkbox" set_as_default_url="{$currency->getSetAsDefaultUrl()}" {if $currency->getIsDefault()}checked="checked"{/if} /></td>
      <td class="name">{$currency->getName()|clean}</td>
      <td class="code">{$currency->getCode()|clean}</td>
      <td class="rate">{$currency->getDefaultRate()|clean} {$currency->getCode()|clean}</td>
      <td class="options">{if $currency->canEdit($logged_user)}{link href=$currency->getEditUrl()}<img src="{image_url name=gray-edit.gif}" alt="" />{/link}{/if} {if $currency->canDelete($logged_user)}{link href=$currency->getDeleteUrl() method=post confirm='Are you sure that you want to delete selected currency?'}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}{/if}</td>
    </tr>
  {/foreach}
  </table>
{else}
  <p class="empty_page">{lang add_url=$add_currency_url}There are no currencies defined. Would you like to <a href=":add_url">create one</a>?{/lang}</p>
{/if}
  {empty_slate name=currencies module=invoicing}
</div>