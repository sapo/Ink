<div class="wrapper">
  <h2 class="label">{lang}Shortcuts{/lang}</h2>
  <div class="box">
    <ul class="menu main_menu">
      <li class="icon_home"><a href="{assemble route=mobile_access}"><span>{lang}Home{/lang}</span></a></li>
      <li class="icon_assignments"><a href="{assemble route=mobile_access_assignments}" class=""><span>{lang}Assignments{/lang}</span></a></li>
      <li class="icon_starred"><a href="{assemble route=mobile_access_starred}" class=""><span>{lang}Starred{/lang}</span></a></li>
      <li class="icon_people"><a href="{assemble route=mobile_access_people}" class=""><span>{lang}People{/lang}</span></a></li>
      <li class="icon_projects"><a href="{assemble route=mobile_access_projects}" class=""><span>{lang}Projects{/lang}</span></a></li>
    </ul>
  </div>
  
  
  {if is_foreachable($pinned_projects)}
  <h2 class="label">{lang}Favorite Projects{/lang}</h2>
  <div class="box">
    <ul class="menu main_menu">
    {foreach from=$pinned_projects item=pinned_project}
      <li style="background-image: url({$pinned_project->getIconUrl()})"><a href="{mobile_access_get_view_url object=$pinned_project}" ><span>{$pinned_project->getName()|clean}</span></a></li>
    {/foreach}
    </ul>
  </div>
  {/if}
</div>