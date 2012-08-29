{title not_lang=true}{lang}Edit Translation File{/lang}: {$translation_file}{/title}
{add_bread_crumb not_lang=true}{lang translation_file=$translation_file}Edit Translation File{/lang}{/add_bread_crumb}

{form action=$form_url method=post ask_on_leave=yes}
<table class="common_table lang_table">
  <tr>
    <th>{lang}Dictionary Word/Sentence{/lang}</th>
    <th></th>
    <th class="input_column">{lang}Translated Word/Sentence{/lang}</th>
  </tr>
{if is_foreachable($prepared_form_data)}
  {foreach from=$prepared_form_data key=prepared_form_data_key item=prepared_form_data_value}
  <tr class='{cycle values="odd,even"}'>
    <td class="dictionary">{$prepared_form_data_value.dictionary_value|clean}</td>
    <td class="copy_arrow"><img src="{image_url name='copy_right.gif'}" alt="copy" /></td>
    <td class="input{if !$prepared_form_data_value.translated_value} new{/if}">{text_field name="form_data[$prepared_form_data_key]" value=$prepared_form_data_value.translated_value}</td>
  </tr>
  {/foreach}
{/if}
</table>
{wrap_buttons}
  {submit}Submit{/submit}
{/wrap_buttons}
{/form}