{title not_lang=yes}{$active_filter->getName()}{/title}
{add_bread_crumb}{$active_filter->getName()}{/add_bread_crumb}

<div id="assignments">
  <div id="assignments_filter">
    <table class="filter">
      <tr>
        <td id="assignments_filter_select">
          {lang}Filter{/lang}: <select name="filter">
          {foreach from=$grouped_filters key=group_name item=filters}
            <optgroup label="{$group_name}">
            {foreach from=$filters item=filter}
              <option value="{$filter->getUrl()}" {if $active_filter->getId() == $filter->getId()}class="current" selected="selected"{/if}>{$filter->getName()|clean}</option>
            {/foreach}
            </optgroup>
          {/foreach}
          </select> 
        </td>
        <td id="assignments_filter_options">
          <span class="tooltip"></span> <a href="{$active_filter->getUrl()}" title="{lang}Toggle Filter Details{/lang}" id="toggle_filter_details"><img src="{image_url name='info-gray.gif'}" alt="" /></a> 
        {if $active_filter->canEdit($logged_user)}
          <a href="{$active_filter->getEditUrl()}" title="{lang}Update Filter{/lang}"><img src="{image_url name=gray-edit.gif}" alt="" /></a> 
        {/if}
        {if $active_filter->canDelete($logged_user)}
          {link href=$active_filter->getDeleteUrl() title="Delete Filter" method=post confirm="Are you sure that you want to delete this filter?"}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}
        {/if}
        </td>
      </tr>
    </table>
  </div>
  
  <div id="assignments_filter_details" style="display: none">
    <p>{lang}This filter displays{/lang}:</p>
    <ul>
      <li>
      {if $active_filter->getUserFilter() == 'anybody'}
        {lang}Tasks assigned to anyone{/lang}.
      {elseif $active_filter->getUserFilter() == 'not_assigneed'}
        {lang}Tasks not assigned to anyone{/lang}.
      {elseif $active_filter->getUserFilter() == 'logged_user'}
        {lang}Tasks assigned to person using this filter{/lang}.
      {elseif $active_filter->getUserFilter() == 'logged_user_responsible'}
        {lang}Tasks person using this filter is responsible for{/lang}.
      {elseif $active_filter->getUserFilter() == 'company'}
        {lang company=$active_filter->getVerboseUserFilterData()}Tasks assigned to members of :company company{/lang}.
      {else}
        {lang to=$active_filter->getVerboseUserFilterData()}Tasks assigned to :to{/lang}.
      {/if}
      </li>
      
      {if $active_filter->getDateFilter() == 'late'}
        <li>{lang}Tasks that are late{/lang}</li>
      {elseif $active_filter->getDateFilter() == 'today'}
        <li>{lang}Tasks that are due today{/lang}</li>
      {elseif $active_filter->getDateFilter() == 'tomorrow'}
        <li>{lang}Tasks that are due tomorrow{/lang}</li>
      {elseif $active_filter->getDateFilter() == 'this_week'}
        <li>{lang}Tasks that are due this week{/lang}</li>
      {elseif $active_filter->getDateFilter() == 'next_week'}
        <li>{lang}Tasks that are due next week{/lang}</li>
      {elseif $active_filter->getDateFilter() == 'this_month'}
        <li>{lang}Tasks that are due this month{/lang}</li>
      {elseif $active_filter->getDateFilter() == 'next_month'}
        <li>{lang}Tasks that are due next month{/lang}</li>
      {elseif $active_filter->getDateFilter() == 'selected_date'}
        <li>{lang from=$active_filter->getDateFrom()}Tasks that are due on :from{/lang}</li>
      {elseif $active_filter->getDateFilter() == 'selected_range'}
        <li>{lang from=$active_filter->getDateFrom() to=$active_filter->getDateTo()}Tasks that are due between :from and :to{/lang}</li>
      {/if}
      
      <li>
      {if $active_filter->getProjectFilter() == 'active'}
        {lang}Tasks from all active projects{/lang}
      {else}
        {lang project=$active_filter->getVerboseProjectFilterData()}Tasks from :project project(s){/lang}
      {/if}
      </li>
      
      <li>
      {if $active_filter->getStatusFilter() == 'active'}
        {lang}Only active tasks{/lang}.
      {elseif $active_filter->getStatusFilter() == 'completed'}
        {lang}Only completed tasks{/lang}.
      {else}
        {lang}Both active and completed tasks{/lang}.
      {/if}
      </li>
    </ul>
    {if $active_filter->getObjectsPerPage()}
      <p>{lang by=$active_filter->getVerboseOrderBy() count=$active_filter->getObjectsPerPage()}Tasks are ordered by :by and system shows :count tasks per page{/lang}</p>
    {else}
      <p>{lang by=$active_filter->getVerboseOrderBy()}Tasks are ordered by :by{/lang}</p>
    {/if}
  </div>
  
  <div id="assignments_list">
  {if is_foreachable($assignments)}
    {if $pagination && ($pagination->getLastPage() > 1)}
    <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{$active_filter->getUrl('-PAGE-')}{/pagination}</span></p>
    {/if}
    <div class="clear"></div>
    
    <table class="assignments">
      <tr>
        <th class="star"></th>
        <th class="checkbox"></th>
        <th class="priority"></th>
        <th class="name">{lang}Name{/lang}</th>
        <th class="project">{lang}Project{/lang}</th>
        <th class="option"></th>
      </tr>
    {foreach from=$assignments item=assignment}
      <tr class="assignment_row {cycle values='odd,even'}">
        <td class="star">{object_star object=$assignment user=$logged_user}</td>
        <td class="checkbox">{link href=$assignment->getCompleteUrl(true) class=complete_assignment}<img src="{image_url name=icons/not-checked.gif}" alt="toggle" />{/link}</td>
        <td class="priority">{object_priority object=$assignment}</td>
        <td class="name">
          {$assignment->getVerboseType()|clean}: {object_link object=$assignment}
          <span class="details block">{object_assignees object=$assignment}{if $assignment->getDueOn()} | {due object=$assignment}.{/if}</span>
        </td>
        <td class="project">{project_link project=$assignment->getProject()}</td>
        <td class="options">
        {object_subscription object=$assignment user=$logged_user} 
        {if module_loaded('timetracking') && timetracking_can_add_for($logged_user, $assignment)}
          {object_time object=$assignment show_time=no} 
        {/if}
        {if $assignment->canEdit($logged_user)}
          {link href=$assignment->getEditUrl() title='Edit...'}<img src='{image_url name=gray-edit.gif}' alt='' />{/link} 
        {/if}
        {if $assignment->canDelete($logged_user)}
          {link href=$assignment->getTrashUrl() title='Move to Trash' class=remove_assignment}<img src='{image_url name=gray-delete.gif}' alt='' />{/link}
        {/if}
        </td>
      </tr>
    {/foreach}
    </table>
  {else}
    <p class="empty_page">{lang}There are no tasks that match selected filter rules{/lang}</p>
  {/if}
  </div>
  
{if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
  <p class="next_page"><a href="{$active_filter->getUrl($pagination->getNextPage())}">{lang}Next Page{/lang}</a></p>
{/if}
  <p class="assignments_filter_rss"><a href="{$active_filter->getRssUrl($logged_user)}">{lang}Track using RSS{/lang}</a></p>
</div>