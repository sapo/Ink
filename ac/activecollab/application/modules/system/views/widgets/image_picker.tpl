<div id="editor_picker_dialog">
  <ul class="top_tabs">
  {if !$disable_upload}
    <li class="selected"><a href="#" id="tab_upload_image"><span>{lang}Upload Image{/lang}</span></a></li>
  {/if}
    <li><a href="#" id="tab_link_image" class="{if $disable_upload}selected{/if}"><span>{lang}Link Existing{/lang}</span></a></li>
  </ul>
  
  <div class="top_tabs_object_list dialog_tabs_content">
    {if !$disable_upload}
    <div id="container_tab_upload_image">
      {form enctype="multipart/form-data" action="$image_picker_url" method="post" id="upload_image_form"}
      
        {wrap field=image}
          {label for=image required=yes}Name{/label}
          {file_field name='image' id=image class='title required'}
        {/wrap}
               
        <input type="hidden" value="upload" name="widget_action"/>
          
        {wrap_buttons}
          {submit}Upload and Insert{/submit}
        {/wrap_buttons}
      {/form}
    </div>
    {/if}
    
    <div id="container_tab_link_image">
      {form enctype="multipart/form-data" action="$image_picker_url" method="post" id="link_image_form"}
        {wrap field=image}
          {label for=image required=yes}Image URL{/label}
          {text_field name='image' id=image class='title required'}
        {/wrap}
        
        {wrap_buttons}
          {submit}Insert Image{/submit}
        {/wrap_buttons}      
      {/form}
    </div>
  </div>
</div>

<script type="text/javascript">
  App.widgets.EditorImagePicker.init('#editor_picker_dialog');
</script>