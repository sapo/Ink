<script type="text/javascript">
  App.data.quick_add_map = {$js_encoded_formatted_map};
  App.data.quick_add_urls = {$js_encoded_quick_add_urls};
</script>

<div id="quick_add">
  <div id="quick_add_step_1">
    <div class="quick_add_col_container height_limited_popup">
      <div class="quick_add_col_left">
        <p><strong>{lang}Choose Project{/lang}</strong></p>
        <div id="project_id" class="list_chooser">
          {foreach from=$formatted_map item=project_map key=project_id}
            <label for="quickadd_project_{$project_id}"><input type="radio" name="project_id" value="{$project_id}" class="input_radio" id="quickadd_project_{$project_id}" />{$project_map.name}</label>
          {/foreach}
        </div>
      </div>
      
      <div class="quick_add_col_right">
        <p><strong>{lang}Choose Object Type{/lang}</strong></p>
        <div id="object_chooser" class="list_chooser">
        
        </div>
      </div>
    </div>

    <div class="wizardbar">
      {button class='continue'}Continue{/button}<a href="#" class="wizzard_back">{lang}Close{/lang}</a>
    </div>
  </div>
  
  <div id="quick_add_step_2">
  </div>
</div>
<script type="text/javascript">
  App.system.QuickAdd.init();
</script>