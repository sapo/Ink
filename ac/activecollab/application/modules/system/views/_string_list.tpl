<div id="{$_string_list_id}" class="string_list">
  <table>
{if is_foreachable($_string_list_value)}
  {counter start=0 name=string_list_num assign=_string_list_num}
  
  {foreach from=$_string_list_value item=_string_list_item}
    <tr class="{cycle values='odd,even'} item">
      <td class="num">#{counter name=string_list_num}{$_string_list_num}</td>
      <td class="value">
        <span>{$_string_list_item|clean}</span>
        <input type="hidden" name="{$_string_list_name}[]" value="{$_string_list_item|clean}" />
      </td>
      <td class="remove"><a href="javascript: return false;"><img src="{image_url name='gray-delete.gif'}" alt="" /></a></td>
    </tr>
  {/foreach}
{else}
    <tr class="odd empty">
      <td colspan="2">{lang}List is Empty{/lang}</td>
    </tr>
{/if}
  </table>
  
  <div class="add_list_item">
    <input type="text" name="add_list_item_name" class="add_list_item_name" value="{lang}Add...{/lang}" /> <input type="image" src="{image_url name='plus-small.gif'}" class="add_list_item_button" />
  </div>
</div>
<script type="text/javascript">
  App.system.StringList.init('{$_string_list_id}', '{$_string_list_name}');
</script>