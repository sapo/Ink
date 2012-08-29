{title}Status{/title}
{add_bread_crumb}Status{/add_bread_crumb}

<div id="status_updates_dialog">   
  <ul class="status_update_top_links">
    <li class="first"></li>
    <li><a href="{assemble route=status_updates}" title="{lang}Browse Archive{/lang}"><img src="{image_url name=archive.gif}" alt="" /></a></li>
    <li><a href="{$rss_url}" title="{lang}Track Using RSS{/lang}"><img src="{image_url name=rss.gif}" alt="" /></a></li>
  </ul>
  <p class="dialog_title">{lang}Recent Messages{/lang}</p>

  <div class="table_wrapper">
    <table class="common_table status_updates_table" id="status_updates_table">
      <tbody>
      {if is_foreachable($status_updates)}
        {foreach from=$status_updates item=status_update}
          {include_template name=_status_row controller=status module=status}
        {/foreach}
      {/if}
      </tbody>
    </table>
  </div>
</div>
 
{form action=$add_status_message_url method=post id=update_status_form}
  {label}What are you doing?{/label}
  {text_field name='status' maxlength=255}
  <img src="{image_url name=dialog_submit.gif module=status}" alt="s" id="status_update_button"/>
  <img src="{image_url name=indicator.gif}" alt="Working" id="status_update_indicator" style="display: none" />
  <script type="text/javascript">
    App.widgets.status_update_dialog.init();
  </script>
{/form}