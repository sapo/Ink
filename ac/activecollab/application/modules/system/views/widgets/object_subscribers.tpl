<div id="object_subscriptions">
{if is_foreachable($people)}
  {foreach from=$people item=project_company}
    <table>
      <tr>
        <th colspan="3">{$project_company.company->getName()|clean}</th>
      </tr>
    {foreach from=$project_company.users item=user}
      <tr class="{cycle values='odd,even'}">
        <td class="avatar"><img src="{$user->getAvatarUrl()}" alt="" /></td>
        <td class="name">{user_link user=$user}</td>
        <td class="subscription">
          <input type="checkbox" class="auto input_checkbox" subscribe_url="{$active_object->getSubscribeUrl($user)}" unsubscribe_url="{$active_object->getUnsubscribeUrl($user)}" {if $active_object->isSubscribed($user)}checked="checked"{/if} />
        </td>
      </tr>
    {/foreach}
    </table>
  {/foreach}
{/if}
</div>