{title}Starred{/title}
{add_bread_crumb}Starred{/add_bread_crumb}

<div {if $request->isAsyncCall()}id="starred_popup" class="height_limited_popup" {else}d id="starred"{/if}>
{if is_foreachable($objects)}
  {if $request->isAsyncCall()}
    <p>{lang}List of your starred objects{/lang}:</p>
    {list_objects objects=$objects id=starred_objects show_checkboxes=no show_header=no}
  {else}
    {form action='?route=starred' method=post}
      {list_objects objects=$objects id=starred_objects}
      <div id="mass_edit">
        <select name="action" class=auto id=starred_objects_action>
          <option value="">{lang}With selected ...{/lang}</option>
          <option value=""></option>
          <option value="unstar">{lang}Unstar{/lang}</option>
          <option value="unstar_and_complete">{lang}Unstar and Complete{/lang}</option>
          <option value=""></option>
          <option value="trash">{lang}Move to Trash{/lang}</option>   
        </select>
        <button class="simple" id="starred_objects_submit" type="submit" class="auto">{lang}Go{/lang}</button>
      </div>
      <div class="clear"></div>
    {/form}
  {/if}
{else}
  <p class="empty_page">{lang}You have no starred objects{/lang}</p>
{/if}
</div>