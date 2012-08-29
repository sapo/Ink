{title}Conflicts{/title}
{add_bread_crumb}Conflicts{/add_bread_crumb}

<div id="incoming_mails">
{if is_foreachable($incoming_mails)}
  {if ($pagination->getLastPage() > 1)}
    <p class="pagination top">
      <span class="inner_pagination">
        {lang}Page{/lang}: {pagination pager=$pagination}{assemble route=incoming_mail page='-PAGE-'}{/pagination}
      </span>
    </p>
    <div class="clear"></div>
  {/if}
  
  {form method=post action=$mass_conflict_resolution_url}
    <table class="common_table incoming_mails_table" id="conflicts">
      <tr>
        <th>{lang}Message{/lang}</th>
        <th>{lang}Conflict{/lang}</th>
        <th></th>
        <th class="checkbox"><input type="checkbox" class="auto master_checkbox input_checkbox" /></th>
      </tr>
      {foreach from=$incoming_mails item=incoming_mail}
        {include_template name=_incoming_mail_row controller=incoming_mail_frontend module=incoming_mail}
      {/foreach}
    </table>
    
    <div id="mass_edit">
      <select name="with_selected" class="auto conflicts_action" id="conflicts_action">
        <option value="">{lang}With selected ...{/lang}</option>
        <option value=""></option>
        <option value="delete">{lang}Delete{/lang}</option>
        </select>
      <button class="simple" id="conflicts_submit" type="submit" class="auto conflicts_submit">{lang}Go{/lang}</button>
    </div>
  {/form}
  
  
  
  {if ($pagination->getLastPage() > 1) && !$pagination->isLast()}
    <p class="next_page"><a href="{assemble route=incoming_mail page=$pagination->getNextPage()}">{lang}Next Page{/lang}</a></p>
  {/if}
{else}
  <p class="empty_page">{lang}There are no messages waiting for review{/lang}</p>
{/if}
</div>