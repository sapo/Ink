{title}Review Uploaded XML{/title}
{add_bread_crumb}Review{/add_bread_crumb}

<dl id="language_details">
  <dt>{lang}Name{/lang}</dt>
  <dd>{$language_name|clean}</dd>
  
  <dt>{lang}Locale{/lang}</dt>
  <dd>{$language_locale|clean}</dd>
  
  <dt>{lang}Made For{/lang}</dt>
  <dd>activeCollab {$language_ac_version}</dd>
</dl>

{form method=post action=$import_url}
  <input type="hidden" name="wizard_step" value="{$next_step}" />
  <input type="hidden" name="attachment_id" value="{$attachment_id}" />
  
  {wrap_buttons}
    {button type="submit"}Finalize Importing{/button}
  {/wrap_buttons}
{/form}