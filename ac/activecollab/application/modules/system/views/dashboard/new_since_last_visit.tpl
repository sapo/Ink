{title}New Since Your Last Visit{/title}
{add_bread_crumb}New{/add_bread_crumb}

<div id="new_since_last_visit">
{if is_foreachable($objects)}
  {if $pagination->getLastPage() > 1}
  <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=new_since_last_visit page='-PAGE-'}{/pagination}</span></p>
  <div class="clear"></div>
  {/if}
  
  <table>
    <tr>
      <th></th>
      <th colspan="2">{lang}What's new{/lang}</th>
    </tr>
  {foreach from=$objects item=object}
    <tr class="{cycle values='odd,even'}">
      <td class="star">{object_star object=$object user=$logged_user}</td>
      <td class="name">
        {$object->getVerboseType()|clean}: {object_link object=$object}
        <span class="details block">
        {action_on_by datetime=$object->getCreatedOn() action='Created' user=$object->getCreatedBy()} 
        {if in_array(strtolower($object->getType()), array('comment', 'task', 'attachment'))}
          {lang}in{/lang} {object_link object=$object->getParent()} 
        {/if}
        </span>
      </td>
      <td class="project">{lang}in{/lang} {project_link project=$object->getProject()}</td>
    </tr>
  {/foreach}
  </table>
  
  <p id="mark_all_read">
    <a href="{assemble route=mark_all_read}" id="mark_all_read_link">{lang}Mark All as Read{/lang}</a>
    {if $request->get('async')}
      &middot; {link href='?route=new_since_last_visit'}Open in Separate Page{/link}
    {/if}
  </p>
  <script type="text/javascript">
    App.system.MarkAllAsRead.init('new_since_last_visit');
  </script>
{else}
  <p class="empty_page">{lang}There is nothing new since your last visit{/lang}</p>
{/if}
</div>