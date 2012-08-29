<div class="icon"><img src="{$active_module->getIconUrl()}" alt="" /></div>
<div class="meta">
  <h2>{$active_module->getName()|humanize}</h2>
  <dl>
    <dt>{lang}Name{/lang}</dt>
    <dd>{$active_module->getDisplayName()|clean}, v{$active_module->getVersion()}</dd>
    
    <dt>{lang}Description{/lang}</dt>
    <dd>{$active_module->getDescription()|clean}</dd>
  </dl>
</div>