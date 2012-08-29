{title not_lang=yes}{$active_language->getName()}{/title}
{add_bread_crumb}View{/add_bread_crumb}

<div id="language">
  <dl id="language_details">
    <dt>{lang}Name{/lang}</dt>
    <dd>{$active_language->getName()|clean}</dd>
    
    <dt>{lang}Locale{/lang}</dt>
    <dd>{$active_language->getLocale()|clean}</dd>
  </dl>
  
  <div id="translation_files">
    <h2 class="section_name"><span class="section_name_span">{lang}Translation Files{/lang}</span></h2>
    <div class="section_container">
    {if is_foreachable($translation_files)}
      <table>
      {foreach from=$translation_files item=translation_file}
        <tr class="{cycle values='odd,even'}">
          <td class="name">{$translation_file|clean}</td>
          <td class="options">
          {if $active_language->isEditable($translation_file)}
            {link href=$active_language->getEditTranslationFileUrl($translation_file)}<img src="{image_url name=gray-edit.gif}" alt="" />{/link}
          {else}
            <span class="details">{lang}Not Editable{/lang}</span>
          {/if}
          </td>
        </tr>
      {/foreach}
      </table>
    {else}
      <p>{lang}There are no translation files defined for this language{/lang}</p>
    {/if}
    </div>
    
    {if is_foreachable($dictionaries)}
    <h2 class="section_name"><span class="section_name_span">{lang}Add Translation File{/lang}</span></h2>
    <div class="section_container">
      {form action=$active_language->getAddTranslationFileUrl() method=post}
        {wrap field=dictionary}
          {label for=select_dictionary required=yes}Dictionary{/label}
          <select name="dictionary" id="select_dictionary">
          {foreach from=$dictionaries item=dictionary}
            <option>{$dictionary|clean}</option>
          {/foreach}
          </select>
        {/wrap}
      
        {wrap_buttons}
          {submit}Submit{/submit}
        {/wrap_buttons}
      {/form}
    </div>
    {/if}
  </div>
</div>