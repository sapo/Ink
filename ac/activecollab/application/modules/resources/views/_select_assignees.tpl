<div class="select_assignees_widget" id="{$_select_assignees_id}">
  <div class="select_assignees_widget_users">
    <p class="details">{lang}No users selected{/lang}</p>
  </div>
  <a href="#" class="assignees_button">{lang}Change{/lang}</a>
</div>
<script type="text/javascript">
  App.widgets.SelectAssignees.init('{$_select_assignees_id}', '{$_select_assignees_name}', {$_select_assignees_company_id}, {$_select_assignees_project_id}, {$_select_assignees_exclude_ids});
{if is_foreachable($_select_assignees_users)}
{foreach from=$_select_assignees_users item=_select_assignees_user}
  App.widgets.SelectAssignees.add_user('{$_select_assignees_id}', '{$_select_assignees_name}', {$_select_assignees_user->getId()}, {var_export var=$_select_assignees_user->getDisplayName()}, {if $_select_assignees_user->getId() == $_select_assignees_owner_id}true{else}false{/if});
{/foreach}
  App.widgets.SelectAssignees.done_adding_users('{$_select_assignees_id}', '{$_select_assignees_name}');
{/if}
</script>