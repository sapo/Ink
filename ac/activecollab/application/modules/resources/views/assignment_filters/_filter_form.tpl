<div class="section_container">
  <div class="col">
    {wrap field=name}
      {label for=filterName required=yes}Name{/label}
      {text_field name='filter[name]' value=$filter_data.name id=filterName}
    {/wrap}
  </div>
  
  <div class="col">
    {wrap field=group_name}
      {label for=filterGroupName}Filter Group{/label}
      {text_field name='filter[group_name]' value=$filter_data.group_name id=filterGroupName}
    {/wrap}
  </div>
  
  <div class="clear"></div>
  
  {wrap field=is_private}
    {label}This filter can be used only by me{/label}
    {yes_no name='filter[is_private]' value=$filter_data.is_private}
    <p class="details">{lang}Select Yes to create a private filter. This filter will not be visible to other users and you will be the only person who can use it.{/lang}</p>
  {/wrap}
</div>

<h2 class="section_name"><span class="section_name_span">{lang}Show Assignments...{/lang}</span></h2>
<div class="section_container">
  <table>
    <tr>
      <td class="filter_select_label">{lang}Assigned to{/lang}</td>
      <td class="filter_select_select">
        <select name="filter[user_filter]" class="filter_async_select" id="filter_persons">
          <option value="anybody" {if $filter_data.user_filter == 'anybody'}selected="selected"{/if}>{lang}Anyone{/lang}</option>
          <option value="not_assigned" {if $filter_data.user_filter == 'not_assigned'}selected="selected"{/if}>{lang}Not Assigned{/lang}</option>
          <option value="logged_user" {if $filter_data.user_filter == 'logged_user'}selected="selected"{/if}>{lang}Person Accessing the Page is Assigned or Responsible{/lang}</option>
          <option value="logged_user_responsible" {if $filter_data.user_filter == 'logged_user_responsible'}selected="selected"{/if}>{lang}Person Accessing the Page is Responsible Only{/lang}</option>
          <option value="company" class="filter_async_option" {if $filter_data.user_filter == 'company'}selected="selected"{/if}>{lang}Member of a Company{/lang} ...</option>
          <option value="selected" class="filter_async_option" {if $filter_data.user_filter == 'selected'}selected="selected"{/if}>{lang}Selected Users{/lang} ...</option>
        </select>
      </td>
      <td class="filter_select_additional">
      {if $filter_data.user_filter == 'company'}
        {select_company name='filter[user_filter_data]' value=$filter_data.user_filter_data}
      {elseif $filter_data.user_filter == 'selected'}
        {select_users name='filter[user_filter_data]' value=$filter_data.user_filter_data}
      {/if}
      </td>
    </tr>
    <tr>
      <td class="filter_select_label">{lang}Due On{/lang}</td>
      <td class="filter_select_select">
        <select name="filter[date_filter]" class="filter_async_select">
          <option value="all" {if $filter_data.date_filter == 'all'}selected="selected"{/if}>{lang}Any Time{/lang}</option>
          <option value="late" {if $filter_data.date_filter == 'late'}selected="selected"{/if}>{lang}Late{/lang}</option>
          <option value="today" {if $filter_data.date_filter == 'today'}selected="selected"{/if}>{lang}Today{/lang}</option>
          <option value="tomorrow" {if $filter_data.date_filter == 'tomorrow'}selected="selected"{/if}>{lang}Tomorrow{/lang}</option>
          <option value="this_week" {if $filter_data.date_filter == 'this_week'}selected="selected"{/if}>{lang}This Week{/lang}</option>
          <option value="next_week" {if $filter_data.date_filter == 'next_week'}selected="selected"{/if}>{lang}Next Week{/lang}</option>
          <option value="this_month" {if $filter_data.date_filter == 'this_month'}selected="selected"{/if}>{lang}This Month{/lang}</option>
          <option value="next_month" {if $filter_data.date_filter == 'next_month'}selected="selected"{/if}>{lang}Next Month{/lang}</option>
          <option value="selected_date" class="filter_async_option" {if $filter_data.date_filter == 'selected_date'}selected="selected"{/if}>{lang}Specific Date{/lang} ...</option>
          <option value="selected_range" class="filter_async_option" {if $filter_data.date_filter == 'selected_range'}selected="selected"{/if}>{lang}Specific Range{/lang} ...</option>
        </select>
      </td>
      <td class="filter_select_additional">
      {if $filter_data.date_filter == 'selected_date'}
        {select_date name='filter[date_from]' value=$filter_data.date_from}
      {elseif $filter_data.date_filter == 'selected_range'}
        <table>
	        <tr>
	          <td>{select_date name='filter[date_from]' value=$filter_data.date_from}</td>
	          <td style="width: 10px; text-align: center;">-</td>
	          <td>{select_date name='filter[date_to]' value=$filter_data.date_to}</td>
	        </tr>
	      </table>
      {/if}
      </td>
    </tr>
    
    <tr>
      <td class="filter_select_label">{lang}In Project{/lang}</td>
      <td class="filter_select_select">
        <select name="filter[project_filter]" class="filter_async_select">
          <option value="active" {if $filter_data.project_filter == 'active'}selected="selected"{/if}>{lang}Active Projects{/lang}</option>
          <option value="selected" class="filter_async_option" {if $filter_data.project_filter == 'selected'}selected="selected"{/if}>{lang}Selected Projects ...{/lang}</option>
        </select>
      </td>
      <td class="filter_select_additional">
      {if $filter_data.project_filter == 'selected'}
        {select_projects name='filter[project_filter_data]' user=$logged_user value=$filter_data.project_filter_data}
      {/if}
      </td>
    </tr>
    
    <tr>
      <td class="filter_select_label">{lang}Status{/lang}</td>
      <td class="filter_select_select">
        <select name="filter[status_filter]">
          <option value="active" {if $filter_data.status_filter == 'active'}selected="selected"{/if}>{lang}Active Only{/lang}</option>
          <option value="completed" {if $filter_data.status_filter == 'completed'}selected="selected"{/if}>{lang}Completed Only{/lang}</option>
          <option value="all" {if $filter_data.status_filter == 'all'}selected="selected"{/if}>{lang}Both Active and Completed{/lang}</option>
        </select>
      </td>
      <td></td>
    </tr>
  </table>
</div>

<h2 class="section_name"><span class="section_name_span">{lang}Display and Order{/lang}</span></h2>
<div class="section_container">
  <div class="col">
    {wrap field=order_by}
      {label for=filterOrderBy required=yes}Order by{/label}
      <select name="filter[order_by]" class="required">
        <option value="priority DESC" {if $filter_data.order_by == 'priority DESC'}selected="selected"{/if}>{lang}Priority, Highest First{/lang}</option>
        <option value="priority ASC" {if $filter_data.order_by == 'priority ASC'}selected="selected"{/if}>{lang}Priority, Lowest First{/lang}</option>
        <option value=""></option>
        <option value="due_on ASC" {if $filter_data.order_by == 'due_on ASC'}selected="selected"{/if}>{lang}Due Date, Late First{/lang}</option>
        <option value="due_on DESC" {if $filter_data.order_by == 'due_on DESC'}selected="selected"{/if}>{lang}Due Date, Late at the End{/lang}</option>
        <option value=""></option>
        <option value="created_on ASC" {if $filter_data.order_by == 'created_on ASC'}selected="selected"{/if}>{lang}Creation Time, Older First{/lang}</option>
        <option value="created_on DESC" {if $filter_data.order_by == 'created_on DESC'}selected="selected"{/if}>{lang}Creation Time, Newer First{/lang}</option>
      </select>
    {/wrap}
  </div>
  
  <div class="col">
    {wrap field=order_by_desc}
      {label for=filterObjectsPerPage required=yes}Objects per Page{/label}
      {text_field name='filter[objects_per_page]' value=$filter_data.objects_per_page id=filterObjectsPerPage class='short required'}
    {/wrap}
  </div>
</div>