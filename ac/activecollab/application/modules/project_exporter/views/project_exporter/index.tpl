{title}Project Exporter{/title}
{add_bread_crumb}Export Project{/add_bread_crumb}
<div id="project_exporter_container">
  {if $is_writable}
    {if $download_exists}
      <div id="download_link_block" class="download_link_block_top" style="display: block;">
        {lang}Previous project archive already exists. You can download it using following link:{/lang}<br />
        <div id="download_link">
          <a href="{$download_url}">{$download_url}</a>
        </div>
      </div>
    {/if}
    <form action={$export_project_url} method=post class="uniForm">
      <div class="blockLabels">
        {if is_foreachable($exportable_modules)}
            <table class="common_table" id="main_table">
            {foreach from=$exportable_modules item=exportable_module}
              <tr class="{cycle values=even,odd}" export_url="{$exportable_module.url}" module="{$exportable_module.module}" id="module_{$exportable_module.module}">
                <td class="status_indicator checkbox">
                  <input type="checkbox" name="modules[]" value="{$exportable_module.module}" class="auto input_checkbox" checked="checked" {if $exportable_module.module == 'system'} disabled="disabled"{/if} />
                </td>
                <td class="module_name">{lang}{$exportable_module.label|ucfirst}{/lang}</td>
                <td class="module_log"></td>
              </tr>
            {/foreach}
              <tr class="{cycle values=odd,even}" export_url="{assemble route=project_exporter_finish_export project_id=$active_project->getId()}" module="finalize" id="module_finalize">
                <td class="status_indicator checkbox">
                  <input type="checkbox" name="modules[]" value="finalize" class="auto input_checkbox" checked="checked" disabled="disabled" />
                </td>
                <td class="module_name">{lang}Finalize{/lang}</td>
                <td class="module_log"></td>
              </tr>
            </table>
        {/if}
        <div class="section_container" id="additional_controls">
          {wrap field='visibility'}
            {label for='visibility'}Export{/label}
            {select_visibility name='visibility' normal_caption=$visibility_normal_caption private_caption=$visibility_private_caption id="visibility" project=$active_project}
          {/wrap}
          {wrap field='compress' id=compress_container}
            {label for='compress'}Compress Output{/label}
            {checkbox_field id='compress' name='compress' checked="checked"} {label class='inline' for='compress'}Compress exported project{/label}
          {/wrap}
        </div>
 
        {wrap_buttons}
          {submit}Export{/submit}
        {/wrap_buttons}
        <input type=hidden name=submitted value=submitted />
      </div>
    </form>
  {/if}
</div>