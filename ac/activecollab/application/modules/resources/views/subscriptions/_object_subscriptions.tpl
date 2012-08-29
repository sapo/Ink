{if $_object_subscription_brief}
  <div id="object_subscriptions_for_{$_object_subscriptions_object->getId()}" popup_url={$_object_subscriptions_popup_url}>
    <div class="object_subscriptions_list_wrapper">
  {if is_foreachable($_object_subscriptions)}
    {if count($_object_subscription_links) == 1}
      {join items=$_object_subscription_links} {lang type=$_object_subscriptions_object->getVerboseType(true)}is subscribed to this :type{/lang}
    {else}
      {join items=$_object_subscription_links} {lang type=$_object_subscriptions_object->getVerboseType(true)}are subscribed to this :type{/lang}
    {/if}
  {else}
    {lang type=$_object_subscriptions_object->getVerboseType(true)}There are no users subscribed to this :type{/lang}
  {/if}
    </div>
  </div>
{else}
<div class="resource object_subscriptions" id="object_subscriptions_for_{$_object_subscriptions_object->getId()}" popup_url={$_object_subscriptions_popup_url}>
  <div class="head">
  {if $_object_subscriptions_object->canEdit($logged_user)}
    <h2 class="section_name"><span class="section_name_span">
      <span class="section_name_span_span">{lang}Subscriptions{/lang}</span>
      <ul class="section_options">
        
        <li>{link href=$_object_subscriptions_object->getSubscriptionsUrl() class=open_manage_subscriptions}Manage / Add{/link}</li>
      </ul>
      <div class="clear"></div>
    </span></h2>
  {else}
    <h2 class="section_name"><span class="section_name_span">{lang}Subscriptions{/lang}</span></h2>
  {/if}
  </div>
  <div class="body object_subscriptions_list_wrapper">
{if is_foreachable($_object_subscriptions)}
  {if count($_object_subscription_links) == 1}
    {join items=$_object_subscription_links} {lang type=$_object_subscriptions_object->getVerboseType(true)}is subscribed to this :type{/lang}.
  {else}
    {join items=$_object_subscription_links} {lang type=$_object_subscriptions_object->getVerboseType(true)}are subscribed to this :type{/lang}.
  {/if}
{else}
    {lang type=$_object_subscriptions_object->getVerboseType(true)}There are no users subscribed to this :type{/lang}
{/if}
  </div>
</div>
{/if}
<script type="text/javascript">
  App.resources.ManageSubscriptions.init('object_subscriptions_for_{$_object_subscriptions_object->getid()}', '{$_object_subscriptions_object->getVerboseType(true)}');
</script>