{title}Backup Settings{/title}
{add_bread_crumb}Backup{/add_bread_crumb}

  <h2 class="section_name"><span class="section_name_span">{lang}Status{/lang}</span></h2>
  <div class="section_container content">
  <ul>
    {if $backup_enabled}
      <li>{lang }Automatic backup is <strong>enabled</strong>{/lang}.</li>
      <li>{lang size=$total_size|filesize}Your next backup will be approximately <strong>:size</strong> in size{/lang}.</li>
    {else}
      <li>{lang }Automatic backup is <strong>disabled</strong>{/lang}.</li>
    {/if}
    {if $backup_how_many_backups > 0}
      <li>{lang number_of_backups=$backup_how_many_backups}:number_of_backups last backups will be kept{/lang}</li>
    {else}
      <li>{lang}All backups will be kept{/lang}</li>
    {/if}
  </ul>
  
  {if is_foreachable($existing_backups)}
    <div class="existing_backups">
      <p><strong>{lang}Existing backups{/lang}:</strong></p>
      <table class="common_table">
        <tr>
          <th>{lang}Backup Date{/lang}</th>
          <th>{lang}Backup Size{/lang}</th>
          <th colspan="2">{lang}Backup State{/lang}</th>
        </tr>
      {foreach from=$existing_backups item=existing_backup}
        <tr class="{cycle values='odd,even'}">
          <td>{$existing_backup.time|datetime}</td>
          <td>{$existing_backup.size|filesize}</td>
          {if instance_of($existing_backup.complete, 'Error')}
          <td colspan="2">
            <span class="backup_corrupted">{$existing_backup.complete->getMessage()}</span>
          </td>
          {else}
          <td>
            <span class="backup_valid">{lang}Valid{/lang}</span>
          </td>
          <td><span class="details">
              {"<br />"|implode:$existing_backup.complete}
            </span></td>
          {/if}
        </tr>
      {/foreach}
      </table>
      <p>{lang size=$backup_dir_size|filesize}Backup folder size: <strong>:size</strong>{/lang}</p>
    </div>
  {/if}
  <div class="clear"></div>
  </div>

  <h2 class="section_name"><span class="section_name_span">{lang}Settings{/lang}</span></h2>
  <div class="section_container">
    {form action=$backup_admin_url method=POST}
      <div class="col">
        {wrap field=how_many_backups}
          {label for=how_many_backups required=yes}How many backups to keep:{/label}
          <select class="required" name="backup[how_many_backups]" id='how_many_backups' value=$backup_data.how_many_backups>
          {foreach from=$how_many_values item=how_many_value}
            {if $how_many_value == $backup_data.how_many_backups}
            <option value="{$how_many_value}" selected="selected">{$how_many_value}</option>
            {else}
            <option value="{$how_many_value}">{$how_many_value}</option>
            {/if}
          {/foreach}
          </select>
        {/wrap}
      </div>
      
      <div class="col">
        {wrap field=enabled}
          {label for=enabled required=yes}Enable backup:{/label}
          {yes_no name='backup[enabled]' id='enabled' value=$backup_data.enabled}
        {/wrap}
      </div>
      <div class="clear"></div>
      
      {wrap_buttons}
        {submit}Submit{/submit}
      {/wrap_buttons}
    {/form}
  </div>
