{title}Modules{/title}
{add_bread_crumb}All Modules{/add_bread_crumb}

<div id="modules_admin">
  {if is_foreachable($modules)}
  <h2 class="section_name"><span class="section_name_span">{lang}Installed modules{/lang}</span></h2>
  <div id="modules" class="section_container">
    <table class="modules_list">
      <thead>
        <th class="icon_medium"></th>
        <th class="name">{lang}Name{/lang}</th>
        <th class="is_system"></th>
        <th class="version">{lang}Version{/lang}</th>
      </thead>
      <tbody>
      {foreach from=$modules item=module}
        <tr class="{cycle values='even, odd'}">
          <td class="icon_medium"><img src="{$module->getIconUrl()}" alt="" /></td>
          <td class="name">
            <a href="{$module->getViewUrl()}">{$module->getDisplayName()|clean}</a>
          {if $module->getDescription()}
            <span class="details block">{$module->getDescription()|lang|clickable}</span>
          {/if}
          </td>
          <td class="is_system">
          {if $module->getIsSystem()}
            <span class="details">{lang}System{/lang}</span>
          {/if}
          </td>
          <td class="version"><span class="details">{$module->getVersion()}</span></td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  </div>
  {else}
  <p class="section_container">{lang}No modules in database. Something is really wrong here :'({/lang}</p>
  {/if}
  
  {if is_foreachable($available_modules)}
  <div id="available_modules">
    <h2 class="section_name"><span class="section_name_span">{lang}Available modules{/lang}</span></h2>
    <div class="section_container">
      <table class="modules_list">
        <thead>
          <th class="icon_medium"></th>
          <th class="name">{lang}Name{/lang}</th>
          <th class="options"></th>
          <th class="version">{lang}Version{/lang}</th>
        </thead>
        <tbody>
        {foreach from=$available_modules item=module}
          <tr class="{cycle values='even, odd'}">
            <td class="icon_medium"><img src="{$module->getIconUrl()}" alt="" /></td>
            <td class="name">
              <a href="{$module->getViewUrl()}">{$module->getDisplayName()|clean}</a>
              {if $module->getDescription()}
              <span class="details block">{$module->getDescription()|lang|clickable}</span>
              {/if}
            </td>
            <td class="options">
            {button href=$module->getInstallUrl()}Install{/button}
            </td>
            <td class="version"><span class="details">{$module->getVersion()}</span></td>
          </tr>
        {/foreach}
        </tbody>
      </table>
    </div>
  </div>
  {/if}
</div>