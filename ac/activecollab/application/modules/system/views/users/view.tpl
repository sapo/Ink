{title not_lang=yes}{$active_user->getDisplayName()}{/title}
{add_bread_crumb}Profile{/add_bread_crumb}
{page_object object=$active_user}

{object_quick_options object=$active_user user=$logged_user}
<div class="user main_object" id="user_details">
  <div class="body">
    {if $active_user->canEdit($logged_user)}
    <a href="{$active_user->getEditAvatarUrl()}" id="select_user_icon">
      <img src="{$active_user->getAvatarUrl(true)}" alt="" class="properties_icon" />
    </a>
    <script type="text/javascript">
      App.widgets.IconPicker.init('edit_icon','select_user_icon', App.lang('Change Avatar'));
    </script>
    {else}
      <img src="{$active_user->getAvatarUrl(true)}" alt="" class="properties_icon" />    
    {/if}
    <dl class="properties">
      <dt>{lang}Name{/lang}</dt>
      <dd>{$active_user->getDisplayName()|clean}</dd>
    
      <dt>{lang}Company{/lang}</dt>
      <dd>
      {if $active_user->getConfigValue('title')}
        {lang title=$active_user->getConfigValue('title') company_url=$active_company->getViewUrl() company_name=$active_company->getName()}:title in <a href=":company_url">:company_name</a>{/lang}
      {else}
        <a href="{$active_company->getViewUrl()}">{$active_company->getName()|clean}</a>
      {/if}
      </dd>
      
    {if $logged_user->isPeopleManager()}
      <dt>{lang}Role{/lang}</dt>
      <dd>{role_name role=$active_user->getRole() user=$logged_user}</dd>
    {/if}
    </dl>
    
    <div class="body content">
      <div id="user_details_contact" class="user_details_body_block">
        <dl class="details_list">
          <dt>{lang}Email{/lang}</dt>
          <dd><a href="mailto:{$active_user->getEmail()}">{$active_user->getEmail()|clean}</a></dd>
        
        {if $active_user->getConfigValue('phone_work')}
          <dt>{lang}Work #{/lang}</dt>
          <dd>{$active_user->getConfigValue('phone_work')|clean}</dd>
        {/if}
        {if $active_user->getConfigValue('phone_mobile')}
          <dt>{lang}Mobile #{/lang}</dt>
          <dd>{$active_user->getConfigValue('phone_mobile')|clean}</dd>
        {/if}
        {if $active_user->getConfigValue('im_type') && $active_user->getConfigValue('im_value')}
          <dt>{$active_user->getConfigValue('im_type')|clean}</dt>
          <dd>{$active_user->getConfigValue('im_value')|clean}</dd>
        {/if}
        </dl>
      </div>
      
      <div id="user_details_time" class="user_details_body_block">
        <dl class="details_list">
        {if $active_user->getId() != $logged_user->getId()}
          <dt>{lang}Last visit{/lang}</dt>
          <dd>{$active_user->getLastActivityOn()|ago}</dd>
        {/if}
        
          <dt>{lang}Local time{/lang}</dt>
          <dd>{user_time user=$active_user datetime=$request_time}</dd>
        </dl>
      </div>
    </div>
  </div>
</div>