<div class="select_projects_widget" id="{$_select_projects_id}">
  <div class="select_projects_widget_projects">
    <p class="details">{lang}No projects selected{/lang}</p>
  </div>
  <a href="#" class="projects_button">{lang}Change{/lang}</a>
</div>
<script type="text/javascript">
  App.widgets.SelectProjects.init('{$_select_projects_id}', '{$_select_projects_name}', {if $_select_projects_active_only}true{else}false{/if}, {if $_select_projects_show_all}true{else}false{/if}, {$_select_projects_exclude_ids});
{if is_foreachable($_select_projects_projects)}
{foreach from=$_select_projects_projects item=_select_projects_project}
  App.widgets.SelectProjects.add_project('{$_select_projects_id}', '{$_select_projects_name}', {$_select_projects_project->getId()}, {var_export var=$_select_projects_project->getName()});
{/foreach}
{/if}
</script>