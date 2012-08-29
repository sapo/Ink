<language>
  <info>
    <ac_version><![CDATA[{$ac_version}]]></ac_version>
    <name><![CDATA[{$active_language->getName()|clean}]]></name>
    <locale><![CDATA[{$active_language->getLocale()|clean}]]></locale>
  </info>
  {if is_foreachable($translations)}
  <translations>
  {foreach from=$translations item=module_translations key=module_name}
    {if is_foreachable($module_translations)}
    <module name="{$module_name|clean}">
    {foreach from=$module_translations item=translation key=phrase}
      <translation phrase="{$phrase|clean}"><![CDATA[{$translation|clean}]]></translation>
    {/foreach}
    </module>
    {/if}
  {/foreach}
  </translations>
  {/if}
</language>