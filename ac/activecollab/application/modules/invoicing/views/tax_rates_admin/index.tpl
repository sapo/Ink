{title}Tax Rates{/title}
{add_bread_crumb}List All{/add_bread_crumb}

<div id="taxrates">
{if is_foreachable($tax_rates)}
  <table class="common_table">
    <tr>
      <th class="name">{lang}Tax{/lang}</th>
      <th class="percentage">{lang}Rate{/lang}</th>
      <th class="options"></th>
    </tr>
  {foreach from=$tax_rates item=tax_rate}
    <tr class="{cycle values='odd,even'}">
      <td class="name">{$tax_rate->getName()|clean}</td>
      <td class="percentage">{$tax_rate->getPercentage()|clean}%</td>
      <td class="options">{if $tax_rate->canEdit($logged_user)}{link href=$tax_rate->getEditUrl()}<img src="{image_url name=gray-edit.gif}" alt="" />{/link}{/if} {if $tax_rate->canDelete($logged_user)}{link href=$tax_rate->getDeleteUrl() method=post confirm='Are you sure that you want to delete this tax rate?'}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}{/if}</td>
    </tr>
  {/foreach}
  </table>
{else}
  <p class="empty_page">{lang add_url=$add_tax_rate_url}There are no tax rates defined. Would you like to <a href=":add_url">create one</a>?{/lang}</p>
{/if}
  {empty_slate name=tax_rates module=invoicing}
</div>