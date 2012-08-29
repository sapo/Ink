{title}Import Language{/title}
{add_bread_crumb}Upload XML{/add_bread_crumb}

{if $import_enabled}
  {form method=post action=$import_url enctype="multipart/form-data"}
    {wrap field=xml}
      {label for=xml}Path to Language XML{/label}
      {file_field name=xml id=xml}
    {/wrap}
    
    <input type="hidden" name="wizard_step" value="{$next_step}" />
    
    {wrap_buttons}
      {button type="submit"}Import{/button}
    {/wrap_buttons}
  {/form}
{else}
  <p>{lang}Importing is not enabled, please review errors{/lang}</p>
{/if}