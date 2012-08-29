<div class="select_users_widget" id="{$_select_users_id}">
  <div class="select_users_widget_users">
    <p class="details">{lang}No users selected{/lang}</p>
  </div>
  <a href="#" class="assignees_button">{lang}Change{/lang}</a>
</div>
<script type="text/javascript">
  App.widgets.SelectUsers.init('{$_select_users_id}', '{$_select_users_name}', {$_select_users_company_id}, {$_select_users_project_id}, {$_select_users_exclude_ids});
{if is_foreachable($_select_users_users)}
{foreach from=$_select_users_users item=_select_users_user}
  App.widgets.SelectUsers.add_user('{$_select_users_id}', '{$_select_users_name}', {$_select_users_user->getId()}, {var_export var=$_select_users_user->getDisplayName()});
{/foreach}
{/if}
</script>