{wrap field=body}
  {label required=yes}Comment{/label}
  {editor_field name='comment[body]' class='validate_callback tiny_value_present' inline_attachments=$comment_data.inline_attachments}{$comment_data.body}{/editor_field}
{/wrap}