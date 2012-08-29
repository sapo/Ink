{if is_foreachable($_list_objects_comments)}
<div id="object_comments" class="object_info">
  <h3>{lang}Comments{/lang}</h3>
  <ul class="comments">
  {foreach from=$_list_objects_comments item=comment}
    <li class="comment">
      <p class="author_info">{project_exporter_user_name user=$comment->getCreatedBy()}<span class="date">{lang}on{/lang} {$comment->getCreatedOn()|datetime}</span></p>
      <div class="body">
        {$comment->getFormattedBody()}
      </div>
      {assign var=attachments value=$comment->getAttachments()}
      {if $attachments}
        <div class="attachments">
        <ul class="attachments">
        {foreach from=$attachments item=attachment}
          <li><a href="{$_list_objects_attachments_url_prefix}{$attachment->getId()}_{$attachment->getName()}">{$attachment->getName()}</a></li>
        {/foreach}
          <li class="clear"></li>
        </ul>
        </div>
      {/if}
      <div class="clear"></div>
    </li>
  {/foreach}
  </ul>
</div>
{/if}