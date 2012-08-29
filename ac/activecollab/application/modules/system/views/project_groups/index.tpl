{title}Project Groups{/title}
{add_bread_crumb}Project Groups{/add_bread_crumb}

<div id="manage_project_groups_list" class="manage_project_groups {if $request->isAsyncCall()}async{/if}">
  <div class="manage_project_groups_table_wrapper">
    <table class="common_table">
  {if is_foreachable($project_groups)}
    {foreach from=$project_groups item=project_group}
      {include_template name=_project_group_row controller=project_groups module=system}
    {/foreach}
  {/if}
    </table>
    <p id="manage_project_groups_empty_list" class="empty_page" {if is_foreachable($project_groups)}style="display: none"{/if}>{lang}There are no project groups{/lang}</p>
  </div>
  
  {if $can_add_project_group}
  <form action="{assemble route=project_groups_add}" method="post" class="add_project_group_form">
    <input type="text" /> <img src="{image_url name='plus-small.gif'}" alt="" title="{lang}New Project Group{/lang}" />
  </form>
  {/if}
</div>

<script type="text/javascript">
  App.system.ManageProjectGroups.init_page('manage_project_groups_list');
</script>