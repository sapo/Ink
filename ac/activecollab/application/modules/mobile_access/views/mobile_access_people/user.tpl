<div class="wrapper">
  <div class="box">
    <div class="object_main_info">
      <div class="icon">
        <img src="{$current_user->getAvatarUrl(true)}" alt="logo" />
      </div>
      <div class="name">
      {$current_user->getDisplayName()|clean}
      </div>
      <div class="clear"></div>
    </div>
    <dl class="object_details">
    {if $current_user->getConfigValue('title')}
      <dt>{lang}Title{/lang}</dt>
      <dd>{$current_user->getConfigValue('title')|clean}</dd>
    {/if}
      <dt>{lang}Email{/lang}</dt>
      <dd><a href="mailto:{$current_user->getEmail()|clean}}">{$current_user->getEmail()|clean}</a></dd>
    {if $current_user->getConfigValue('phone_work')}
      <dt>{lang}Work Phone{/lang}</dt>
      <dd>{$current_user->getConfigValue('phone_work')|clean}</dd>
    {/if}
    {if $current_user->getConfigValue('phone_mobile')}
      <dt>{lang}Mobile Phone{/lang}</dt>
      <dd>{$current_user->getConfigValue('phone_mobile')|clean}</dd>
    {/if}
    {if $current_user->getConfigValue('im_type') && $current_user->getConfigValue('im_value')}
      <dt>{$current_user->getConfigValue('im_type')|clean}</dt>
      <dd>{$current_user->getConfigValue('im_value')|clean}</dd>
    {/if}
    </dl>
  </div>
</div>