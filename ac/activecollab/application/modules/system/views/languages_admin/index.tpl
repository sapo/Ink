{title}Languages{/title}
{add_bread_crumb}Details{/add_bread_crumb}

<div id="languages_administration">
  <div id="languages">
    <div class="col_wide">
      <h2 class="section_name"><span class="section_name_span">{lang}Available Languages{/lang}</span></h2>
      <div class="section_container">
      {if is_foreachable($languages)}
        <table class="common_table">
        {foreach from=$languages item=language}
          <tr class="{cycle values='odd,even'}">
            <td class="checkbox"><input type="checkbox" class="auto input_checkbox" set_as_default_url="{$language->getSetAsDefaultUrl()}" title="{lang}Default Language?{/lang}" {if $default_language_id == $language->getId()}checked="checked"{/if} /></td>
            <td class="name"><a href="{$language->getViewUrl()}">{$language->getName()|clean}</a></td>
            <td class="options">
              {link href=$language->getExportUrl()}<img src="{image_url name=gray-export.gif}" alt="" />{/link}
              {link href=$language->getEditUrl()}<img src="{image_url name=gray-edit.gif}" alt="" />{/link}
              {if $language->canDelete($logged_user)}{link href=$language->getDeleteUrl() method=post confirm='This will permanently remove this language. Are you sure?'}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}{/if}
            </td>
          </tr>
          </tr>
        {/foreach}
        </table>
      {else}
        <p>{lang}There are no languages installed in the system{/lang}</p>
      {/if}
    </div>
  </div>
  
  <div class="col_wide2">
    <div id="add_language">
      <h2 class="section_name"><span class="section_name_span">{lang}New Language{/lang}</span></h2>
      <div class="section_container">
        {form action='?route=admin_languages_add' method=post autofocus=no}
          {include_template name=_language_form controller=languages_admin module=system}
          {wrap_buttons}
            {submit}Submit{/submit}
          {/wrap_buttons}
        {/form}
      </div>
    </div>
  </div>
    
  <div class="clear"></div>
</div>