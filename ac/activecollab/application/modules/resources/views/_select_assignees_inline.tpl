<div class="select_asignees_inline_widget" id="{$_select_assignees_id}">
{if $_select_assignees_choose_responsible}
  <input type="hidden" name="{$_select_assignees_responsible_name}" value="{$_select_assignees_responsible}" id="{$_select_assignees_id}_responsible" />
  <div class="select_asignees_inline_widget_responsible_block">
    <span class="placeholder">{lang}No one is responsible{/lang}</span>
  </div>
{/if}
{foreach from=$_select_assignees_users key=company item=users}
  {if is_foreachable($users)}
    <div class="user_group">
      <label class="company_name" for="{$_select_assignees_id}_company_{$company|clean}"><input type="checkbox" name="" value="" id="{$_select_assignees_id}_company_{$company|clean}}" class="input_checkbox" /><span>{lang company_name=$company}All of :company_name{/lang}</span></label>
      <div class="company_users">
        <table>
          <tr>
          {foreach from=$users item=user name=users_loop}
            {if ($smarty.foreach.users_loop.index % $_select_assignees_users_per_row == 0) && ($smarty.foreach.users_loop.index !=0)}
              </tr><tr>
            {/if}
            <td><span class="company_user">
              <input type="checkbox" name="{$_select_assignees_name}[]" value="{$user.id}" id="{$_select_assignees_id}_user_{$user.id}" {if in_array($user.id, $_select_assignees_assigned)}checked="checked"{/if} class="input_checkbox"/>
              {if $_select_assignees_choose_responsible && ($_select_assignees_responsible == $user.id)}
                <span class="responsible_setter responsible">{$user.display_name|clean}</span>
              {else}
                <span class="responsible_setter">{$user.display_name|clean}</span>
              {/if}
            </span></td>
          {/foreach}
          </tr>
        </table>
      </div>
    </div>
  {/if}
{/foreach}
</div>
<script type="text/javascript">
  var picker_wrapper = $('#{$_select_assignees_id}:first');
  App.widgets.SelectAsigneesInlineWidget.init(picker_wrapper, '#{$_select_assignees_id}',{var_export var=$_select_assignees_choose_responsible}{if $_select_assignees_choose_responsible && $_select_assignees_responsible}, {$_select_assignees_responsible}{/if});
</script>