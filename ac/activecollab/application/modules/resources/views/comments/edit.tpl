{title}Edit Comment{/title}
{add_bread_crumb}Edit Comment{/add_bread_crumb}

{form action=$active_comment->getEditUrl() method=post ask_on_leave=yes}
  {wrap field=body}
    {label required=yes}Comment{/label}
    {editor_field name='comment[body]' class='validate_callback tiny_value_present' inline_attachments=$comment_data.inline_attachments}{$comment_data.body}{/editor_field}
  {/wrap}

  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}