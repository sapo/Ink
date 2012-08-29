{title}Change PDF Settings{/title}
{add_bread_crumb}Change{/add_bread_crumb}

<script type="text/javascript" src="{$assets_url}/javascript/jquery.colorpicker.js"></script>
<link rel="stylesheet" href="{$assets_url}/stylesheets/colorpicker.css" type="text/css" media="screen"/>

{form action=$pdf_settings_url method=POST}
  <div class="col_wide">
    <h2 class="section_name"><span class="section_name_span">{lang}Paper Settings{/lang}</span></h2>
    <div class="section_container">
      {wrap field=paper_format}
        {label for=paper_format required=yes}Paper Size:{/label}
        {if is_foreachable($paper_formats)}
          <select name="pdf_settings[paper_format]">
          {foreach from=$paper_formats item=paper_format}
            {if $pdf_settings_data.paper_format == $paper_format}
              <option value="{$paper_format}" selected="selected">{$paper_format}</option>
            {else}
              <option value="{$paper_format}">{$paper_format}</option>
            {/if}
          {/foreach}
          </select>
        {/if}
      {/wrap}
      {wrap field=paper_orientation}
        {label for=paper_orientation required=yes}Paper Orientation:{/label}
        {if is_foreachable($paper_formats)}
          <select name="pdf_settings[paper_orientation]">
          {foreach from=$paper_orientations item=paper_orientation}
            {if $pdf_settings_data.paper_orientation == $paper_orientation}
              <option value="{$paper_orientation}" selected="selected">{$paper_orientation}</option>
            {else}
              <option value="{$paper_orientation}">{$paper_orientation}</option>
            {/if}
          {/foreach}
          </select>
        {/if}
      {/wrap}
    </div>
  </div>
  <div class="col_wide">
    <h2 class="section_name"><span class="section_name_span">{lang}Colors and Styles{/lang}</span></h2>
    <div class="section_container">
      {wrap field=header_text_color}
        {label for=header_text_color}Header and Footer Text Color:{/label}
        <div class="select_color">
          {text_field name='pdf_settings[header_text_color]' value=$pdf_settings_data.header_text_color id=header_text_color class='short'}
          <div class="color_selector"><div style="background-color: rgb(136, 136, 207);"></div></div>
        </div>
      {/wrap}
      {wrap field=page_text_color}
        {label for=page_text_color}Page Text Color:{/label}
        <div class="select_color">
          {text_field name='pdf_settings[page_text_color]' value=$pdf_settings_data.page_text_color id=page_text_color class='short'}
          <div class="color_selector"><div style="background-color: rgb(136, 136, 207);"></div></div>
        </div>
      {/wrap}
      {wrap field=border_color}
        {label for=border_color}Border Color:{/label}
        <div class="select_color">
          {text_field name='pdf_settings[border_color]' value=$pdf_settings_data.border_color id=border_color class='short'}
          <div class="color_selector"><div style="background-color: rgb(136, 136, 207);"></div></div>
        </div>
      {/wrap}
      {wrap field=background_color}
        {label for=background_color}Table Header Background Color:{/label}
        <div class="select_color">
          {text_field name='pdf_settings[background_color]' value=$pdf_settings_data.background_color id=background_color class='short'}
          <div class="color_selector"><div style="background-color: rgb(136, 136, 207);"></div></div>
        </div>
      {/wrap}
    </div>
  </div>
  <div class="clear"></div>
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}
