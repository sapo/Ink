{title}Search{/title}
{add_bread_crumb}Search{/add_bread_crumb}

<div id="search">
  {form action="?route=search" method="get" show_errors=no id=search_form}
  <table class="search_form stripped_background">
    <tr>
      <td class="search_form_caption">{lang}Search{/lang}:</td>
      <td class="search_for">
      {wrap field=search_for}
        {text_field name=q id=search_for_input value=$search_for class=required}
      {/wrap}
      </td>
      <td class="search_type">
      {wrap field=search_type}
        <select name="type" id="search_for_type">
          <option value="in_projects" {if $search_type == 'in_projects'}selected="selected"{/if}>{lang}In projects{/lang}</option>
          <option value="for_people" {if $search_type == 'for_people'}selected="selected"{/if}>{lang}For users{/lang}</option>
          <option value="for_projects" {if $search_type == 'for_projects'}selected="selected"{/if}>{lang}For projects{/lang}</option>
        </select>
      {/wrap}
      </td>
      <td class="search_form_button">{submit class='grey_button'}Go{/submit}</td>
    </tr>
  </table>
  {/form}
  
{if $search_for && $search_type}
  <div class="clear"></div>
  {if is_foreachable($search_results)}
  <div id="search_results">
    <p class="pagination top">
      <span class="inner_pagination">
      {lang}Page{/lang}: {pagination pager=$pagination}{assemble route=search q=$search_for type=$search_type page='-PAGE-'}{/pagination}
      </span>
    </p>
    
    <div class="clear"></div>
    
    {if $search_type == 'in_projects'}
      {list_objects objects=$search_results show_checkboxes=no show_header=no id=search_results}
    {elseif $search_type == 'for_people'}
    <table id="people_list">
    {foreach from=$search_results item=user}
      <tr class="{cycle values='odd,even'}">
        <td class="avatar"><img src="{$user->getAvatarUrl()}" alt="" /></td>
        <td class="name">{user_link user=$user}</td>
        <td class="email"><a href="mailto:{$user->getEmail()|clean}">{$user->getEmail()|clean}</a></td>
      </tr>
    {/foreach}
    </table>
    {elseif $search_type == 'for_projects'}
    <table id="projects_list">
    {foreach from=$search_results item=project}
      <tr class="{cycle values='odd,even'}">
        <td class="icon"><img src="{$project->getIconUrl()}" alt="" /></td>
        <td class="name"><a href="{$project->getOverviewUrl()}">{$project->getName()|clean}</a></td>
        <td class="progress">{project_progress project=$project info=no}</td>
      </tr>
    {/foreach}
    </table>
    {/if}
  </div>
  {else}
    <p class="empty_page">{lang for=$search_for}Search failed to find any object that match your request{/lang}</p>
  {/if}
{/if}
  
</div>