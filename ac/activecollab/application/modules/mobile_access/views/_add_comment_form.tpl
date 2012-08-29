  <div class="box">
    <form action="{$_mobile_access_add_comment_form_add_comment_url}" method=post class='simple_form' id='quick_add_comment'>
      {wrap field=body class="ctrlHolderNoBottomPadding"}
        {label for=commentBody required=yes}Your Comment{/label}
          {textarea_field name='comment[body]' id=quick_add_discussion_comment class=required}{$_mobile_access_add_comment_form_comment_data.body}{/textarea_field}
      {/wrap}
      
      <input type="hidden" name="submitted" value="submitted" />
      
      {wrap_buttons}
        {submit}Comment{/submit}
      {/wrap_buttons}
    </form>
  </div>