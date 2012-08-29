  <div class="col_wide">
  {wrap field=note_name}
    {label for=note_name required=yes}Choose Invoice Note:{/label}
    {text_field name='note[name]' value=$note_data.name id=note_name class='title required'}
  {/wrap}
 
  {wrap field=note_content}
    {label for=note_content required=yes}Note Content:{/label}
    {textarea_field name='note[content]' id='note_content' class='long required'}{$note_data.content}{/textarea_field}
    <p class="details boxless">{lang}HTML not supported! Line breaks are preserved.{/lang}</p>
  {/wrap}
  </div>
  <div class="clear"></div>