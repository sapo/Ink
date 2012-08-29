{title}Roles Management{/title}
{add_bread_crumb}All roles{/add_bread_crumb}

<div id="roles_administration">
  <div id="system_roles">
    <div class="col_wide">
      <h2 class="section_name"><span class="section_name_span">{lang}System Roles{/lang}</span></h2>
      <div class="section_container">
      {if is_foreachable($system_roles)}
        <table class="common_table">
        {foreach from=$system_roles item=role}
          <tr class="{cycle values='odd,even'}">
            <td class="checkbox"><input type="checkbox" class="auto input_checkbox" set_as_default_url="{$role->getSetAsDefaultUrl()}" title="{lang}Default Role?{/lang}" {if $default_role_id == $role->getId()}checked="checked"{/if} /></td>
            <td class="name"><a href="{$role->getViewUrl()}">{$role->getName()|clean}</a></td>
            <td class="options">{link href=$role->getEditUrl()}<img src="{image_url name=gray-edit.gif}" alt="" />{/link}{if $role->canDelete()} {link href=$role->getDeleteUrl() method=post confirm='Are you sure that you want to delete selected role?'}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}{/if}</td>
          </tr>
       {/foreach}
        </table>
      {else}
      <p>{lang}There are no system roles defined{/lang}</p>
      {/if}
      </div>
    </div>
  </div>
  
  <div class="col_wide2">
    <div id="projects_roles">
      <h2 class="section_name"><span class="section_name_span">{lang}Project Roles{/lang}</span></h2>
      <div class="section_container">
      {if is_foreachable($project_roles)}
        <table class="common_table">
        {foreach from=$project_roles item=role}
          <tr class="{cycle values='odd,even'}">
            <td class="name"><a href="{$role->getViewUrl()}">{$role->getName()|clean}</a></td>
            <td class="options">{link href=$role->getEditUrl()}<img src="{image_url name=gray-edit.gif}" alt="" />{/link}{if $role->canDelete()} {link href=$role->getDeleteUrl() method=post confirm='Are you sure that you want to delete selected role?'}<img src="{image_url name=gray-delete.gif}" alt="" />{/link}{/if}</td>
          </tr>
        {/foreach}
        </table>
      {else}
        <p class="empty_page">{lang}There are no project roles defined{/lang}</p>
      {/if}
      </div>
    </div>
  </div>
    
  <div class="clear"></div>
  
  {empty_slate name=roles module=system}
</div>