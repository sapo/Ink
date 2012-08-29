{if is_foreachable($_mobile_access_comments_comments)}
<h2 class="label" id="comments">{lang}Comments{/lang}</h2>
<div class="box">
  <div class="comments">
    {counter start=$_mobile_access_comments_counter skip=1 assign=counter_var}
    {foreach from=$_mobile_access_comments_comments item=comment}
      <div class="comment {cycle values=odd,even}" id="comment_{$comment->getId()}">
      {assign var=comment_author value=$comment->getCreatedBy()}
        <div class="author">
        {if $_mobile_access_comments_show_counter}
          <div class="comment_number">
            #{$counter_var}
          </div>
        {/if}
          <img src="{$comment_author->getAvatarUrl()}" class="author_icon">
          <a href="{mobile_access_get_view_url object=$comment_author}" class="author_link">{$comment_author->getName()|clean}</a>
          <div class="clear"></div>
        </div>
        <div class="date">
        {$comment->getCreatedOn()|datetime}
        </div>
        <div class="content formatted">{$comment->getFormattedBody()}</div>
        {mobile_access_object_attachments object=$comment}
      </div>
      {counter}
    {/foreach}
    {mobile_access_paginator paginator=$_mobile_access_comments_paginator url=$_mobile_access_comments_url anchor='#comments'}
  </div>
</div>
{/if}