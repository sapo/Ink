<div class="wrapper">
  <div class="box">
    <div class="object_main_info">
      <div class="icon">
        <img src="{$current_company->getLogoUrl(true)}" alt="logo" />
      </div>
      <div class="name">
      {$current_company->getName()|clean}
      </div>
      <div class="clear"></div>
    </div>
    <dl class="object_details">
    {if $current_company->getConfigValue('office_address')}
      <dt>{lang}Address{/lang}</dt>
      <dd>{$current_company->getConfigValue('office_address')|clean|nl2br}</dd>
    {/if}
    {if $current_company->getConfigValue('office_phone')}
      <dt>{lang}Phone Number{/lang}</dt>
      <dd>{$current_company->getConfigValue('office_phone')|clean}</dd>
    {/if}
    {if $current_company->getConfigValue('office_fax')}
      <dt>{lang}Fax Number{/lang}</dt>
      <dd>{$current_company->getConfigValue('office_fax')|clean}</dd>
    {/if}
    {if is_valid_url($current_company->getConfigValue('office_homepage'))}
      <dt>{lang}Homepage{/lang}</dt>
      <dd><a href="{$current_company->getConfigValue('office_homepage')}">{$current_company->getConfigValue('office_homepage')|clean}</a></dd>
    {/if}
    </dl>
  </div>
  
  {if is_foreachable($current_company_users)}
  <h2 class="label">{lang}Users{/lang}</h2>
  <div class="box">
    <ul class="menu main_menu">
    {foreach from=$current_company_users item=user}
      <li style="background-image: url({$user->getAvatarUrl()})"><a href="{mobile_access_get_view_url object=$user}"><span>{$user->getName()|clean|excerpt:22}</span></a></li>
    {/foreach}
    </ul>
  </div>
  {/if}
  
  {if is_foreachable($current_company_projects)}
  <h2 class="label">{lang}Projects{/lang}</h2>
  <div class="box">
    <ul class="menu main_menu">
    {foreach from=$current_company_projects item=project}
      <li style="background-image: url({$project->getIconUrl()})"><a href="{mobile_access_get_view_url object=$project}"><span>{$project->getName()|clean|excerpt:22}</span></a></li>
    {/foreach}
    </ul>
  </div>
  {/if}
  
  
</div>