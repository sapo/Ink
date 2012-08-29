{if is_foreachable($_mobile_access_object_attachments)}
<div class="attachments">
  <ul>
  {foreach from=$_mobile_access_object_attachments item=_attachment}
    <li><a href="{$_attachment->getViewUrl()}">{$_attachment->getName()|clean} <span class="details">({$_attachment->getSize()|filesize})</span></a></li>
  {/foreach}
  </ul>
</div>
{/if}