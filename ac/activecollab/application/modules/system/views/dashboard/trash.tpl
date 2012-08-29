{title}Trash{/title}
{add_bread_crumb}Trash{/add_bread_crumb}

<div id="trash">
{if is_foreachable($objects)}
  {if $pagination->getLastPage() > 1}
  <p class="pagination top"><span class="inner_pagination">{lang}Page{/lang}: {pagination pager=$pagination}{assemble route=trash page='-PAGE-'}{/pagination}</span></p>
  <div class="clear"></div>
  {/if}

  {form action='?route=trash' method=post autofocus=no uni=no id='trashed_objects_form'}
  {list_objects objects=$objects id=trashed_objects}
  <div id="trash_options">
    <div id="mass_edit">
      <select name=action class=auto id=trashed_objects_action>
        <option value="">{lang}With selected ...{/lang}</option>
        <option value=""></option>
        <option value="restore">{lang}Restore{/lang}</option>
        <option value="delete">{lang}Delete permanently{/lang}</option>
      </select>
      <button class="simple" id="trashed_objects_submit" type="submit" class="auto">{lang}Go{/lang}</button>
    </div>
  </div>
  {/form}
{else}
  <p class="empty_page">{lang}Trash is empty{/lang}</p>
{/if}
</div>