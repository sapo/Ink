<div id="{$widget_id}_popup" class="select_users_widget_popup">
  <table class="select_users_layout">
    <tr>
      <td class="users_list">
        <p>{lang}Available users{/lang}:</p>
      {if is_foreachable($grouped_users)}
        <select multiple="multiple">
        {foreach from=$grouped_users key=company_name item=users}
          <optgroup label="{$company_name|clean}">
          {foreach from=$users item=user}
            <option value="{$user.id}" class="{cycle values='odd,even'}">{$user.display_name|clean}</option>
          {/foreach}
          </optgroup>
        {/foreach}
        </select>
      {else}
        <p class="details">{lang}No users here{/lang}</p>
      {/if}
      </td>
      <td class="divider">
        <img src="{image_url name=arrow-right.gif}" alt="" />
      </td>
      <td class="selected_users">
        <div class="selected_users_list" {if !is_foreachable($selected_users)}style="display: none"{/if}>
          <p>{lang}Selected users{/lang}:</p>
          <div class="selected_users_list_container">
            <table>
            {if is_foreachable($selected_users)}
            {foreach from=$selected_users item=user}
              <tr id="{$widget_id}_user_{$user->getId()}" class="{cycle values='odd,even' name=$selected_users_cycle_name}">
                <td class="display_name">{lang username=$user->getDisplayName() company=$user->getCompanyName()}<span>:username</span> of :company{/lang}</td>
                <td class="remove"><img src="{image_url name=gray-delete.gif}" alt="" title="{lang}Remove from the list{/lang}" /></td>
              </tr>
            {/foreach}
            {/if}
            </table>
          </div>
        </div>
        <p class="no_users_selected" {if is_foreachable($selected_users)}style="display: none"{/if}>
          {lang}No users selected{/lang}
          <br /><br />
          <span>{lang}Select users from the list on the left and click the arrow button to mark them as selected{/lang}</span>
        </p>
      </td>
    </tr>
  </table>
</div>