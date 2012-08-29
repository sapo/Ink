{title}Install Module{/title}
{add_bread_crumb}Install{/add_bread_crumb}

<div id="install_module">
{if $can_be_installed}
  <p>{lang name=$active_module->getDisplayName()}All checks passed. :name module <strong>can be installed</strong>{/lang}.</p>
{else}
  <p>{lang name=$active_module->getDisplayName()}We are sorry, but <strong>:name module can't be installed</strong> because:{/lang}</p>
{/if}
  {if is_foreachable($installation_check_log)}
  <ol>
  {foreach from=$installation_check_log item=log_message}
    <li>{$log_message|clean|clickable}</li>
  {/foreach}
  </ol>
  {/if}
{if $can_be_installed}
  <div id="install_module_button">
    {button href=$active_module->getInstallUrl() method=post confirm='Are you sure that you want to install this module?'}{lang name=$active_module->getDisplayName()}Install :name Module{/lang}{/button}
  </div>
{else}
  <p>{lang}Please fix the errors listed above to be able to install this module{/lang}.</p>
{/if}
</div>