{if is_foreachable($companies)}
  <ul class="list_with_icons">
  {foreach from=$companies item=company}
    <li class="obj_link {if $owner_company->getId() == $company->getId()}owner_company{/if}">
      <a href="{mobile_access_get_view_url object=$company}">
        <span class="main_line">
          <img src="{$company->getLogoUrl(true)}" alt="logo" class="icon" />
          {$company->getName()|clean|excerpt:22}
        </span>
      </a>
    </li>
  {/foreach}
  </ul>
  {mobile_access_paginator paginator=$pagination url=$pagination_url}
{else}
  <div class="wrapper">
    <div class="box">
      <ul class="menu">
        <li>{lang}No Companies{/lang}</li>
      </ul>
    </div>
  </div>
{/if}