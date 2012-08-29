{if instance_of($active_object, 'ProjectObject')}
  {title not_lang=yes}{lang name=$active_object->getName()}":name" Time Data{/lang}{/title}
{else}
  {title}Time{/title}
{/if}

{add_bread_crumb}Log{/add_bread_crumb}

<div id="timerecords">
  {if $pagination->getLastPage() > 1}
  <p class="pagination top">
  {if instance_of($active_object, 'ProjectObject')}
    <span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=project_time page='-PAGE-' project_id=$active_project->getId() for=$active_object->getId()}{/pagination}</span>
  {else}
    <span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=project_time page='-PAGE-' project_id=$active_project->getId()}{/pagination}</span>
  {/if}
  </p>
  <div class="clear"></div>
  {/if}
  
  {form action=$add_url method=post id=add_time_record_form show_errors=no}
      <table class="common_table timerecords">
        <thead>
          <tr>
            <th class="date">{lang}Date{/lang}</th>
            <th class="user">{lang}Person{/lang}</th>
            <th class="hours">{lang}Hours{/lang}</th>
            <th class="desc">{lang}Summary{/lang}</th>
            <th class="billable">{lang}Billable{/lang}</th>
            <th class="actions"></th>
            <th class="checkbox"></th>
          </tr>
        </thead>
        <tbody>
        {if $can_add}
          <tr id="new_record">
            <td class="date">
            {wrap field=record_date}
              {select_date name='time[record_date]' value=$timetracking_data.record_date id=start_date show_timezone=no class='auto required'}
            {/wrap}
            </td>
            <td class="user">
            {wrap field=user_id}
              {select_user name='time[user_id]' value=$timetracking_data.user_id project=$active_project optional=no id=time_user class='auto required'}
            {/wrap}
            </td>
            <td class="hours">
            {wrap field=value}
              {text_field name='time[value]' value=$timetracking_data.value id=time_hours class='short required'}
            {/wrap}
            </td>
            <td class="desc">
            {wrap field=body}
              {text_field name='time[body]' value=$timetracking_data.body id=time_summary}
            {/wrap}
            </td>
            <td class="billable">
            {wrap field=billable_status}
              {checkbox_field name=time[billable_status] value=$timetracking_data.billable_status title='Billable...' checked=yes}
            {/wrap}
            </td>
            <td class="actions" colspan="2">{submit class='grey_button'}Add{/submit}</td>
          </tr>
        {/if}
        {if is_foreachable($timerecords)}
          {foreach from=$timerecords item=timerecord}
            {include_template name=_time_row controller=timetracking module=timetracking}
          {/foreach}
        {/if}
      </tbody>
    </table>
    
    {if $can_manage}
    <div id="mass_edit">
      <select name="action" class="auto" id="records_action">
        <option value="">{lang}With selected ...{/lang}</option>
        <option value=""></option>
        
        <option value="mark_as_billable">{lang}Mark as Billable{/lang}</option>
        <option value="mark_as_not_billable">{lang}Mark as Non-Billable{/lang}</option>
        <option value=""></option>
        
      {if $logged_user->getProjectPermission('timerecord', $active_project) >= PROJECT_PERMISSION_MANAGE}
        <option value="mark_as_billed">{lang}Mark as Billed{/lang}</option>
        <option value="mark_as_not_billed">{lang}Mark as Not Billed{/lang}</option>
        <option value=""></option>
      {/if}
        <option value="move_to_trash">{lang}Move to Trash{/lang}</option>
      </select>
      <button class="simple" id="records_submit" type="button" class="auto">{lang}Go{/lang}</button>
    </div>
    {/if}
  {/form}
  
  <div class="clear"></div>
  
  {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
    {if instance_of($active_object, 'ProjectObject')}
      <p class="next_page"><a href="{assemble route=project_time page=$pagination->getNextPage() project_id=$active_project->getId() for=$active_object->getId()}">{lang}Next Page{/lang}</a></p>
    {else}
      <p class="next_page"><a href="{assemble route=project_time page=$pagination->getNextPage() project_id=$active_project->getId()}">{lang}Next Page{/lang}</a></p>
    {/if}
  {/if}
  
  
{if !is_foreachable($timerecords)}
  <script type="text/javascript">
    $('#mass_edit').hide();
  </script>
  
  <div id="no_records">
    <p class="empty_page">{lang}No time records here{/lang}. {if $can_add}{lang}Use the form above to create new ones{/lang}.{/if}</p>
    {empty_slate name=time module=timetracking}
  </div>
{/if}
</div>