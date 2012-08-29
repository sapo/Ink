{title}Subscriptions{/title}
{add_bread_crumb}Subscriptions{/add_bread_crumb}

<div id="object_subscriptions" class="height_limited_popup">
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
        <td class="subscription">{object_subscription object=$active_object user=$user}</td>
      </tr>
    {/foreach}
    </table>
  {/foreach}
{/if}
</div>