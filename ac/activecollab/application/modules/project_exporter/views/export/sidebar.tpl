<div id="sidebar">
  {if is_foreachable($_project_exporter_exportable_modules)}
    <ul id="exported_modules">
    {foreach from=$_project_exporter_exportable_modules item=_exportable_module}
      {if $_exportable_module.module == $active_module}
        {assign var=selected value='selected'}
      {else}
        {assign var=selected value=''}
      {/if}
      {if $_exportable_module.module=='system'}
      <li><a href="{$url_prefix}index.html" class="{$selected}">{lang}Overview{/lang}</a></li>
      {else}
      <li><a href="{$url_prefix}{$_exportable_module.module}/index.html" class="{$selected}">{lang}{$_exportable_module.label|ucfirst}{/lang}</a></li>
      {/if}
    {/foreach}
    </ul>
  {/if}
  
  <div class="copy">
    <p>&copy;{year} by {$owner_company->getName()|clean}</p>
  </div>
</div>

<div id="content_container">