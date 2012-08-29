{title}Source Settings{/title}

  <div class="section_container">
    {form action=$backup_admin_url method=POST}
      <div class="col_wide">
        {wrap field=svn_path}
          {label for=svn_path required=no}SVN Location{/label}
          {text_field id='svn_path' name=source[svn_path] value=$source_data.svn_path}
          <p class="details">Enter path for subversion executable (without executable name)</p>         
        {/wrap}
      </div>
      
      <div class="col_wide">
        <div class="admin_test_setting" id="check_svn_path">
          <button type="button"><span><span>{lang}Check SVN Path{/lang}</span></span></button>
          <span class="test_results">
            <span></span>
          </span>
        </div>
      </div>

      <div class="clear"></div>
      
      <div class="col_wide">
        {wrap field=svn_config_dir}
          {label for=svn_config_dir required=no}SVN Config Directory Path{/label}
          {text_field id='svn_config_dir' name=source[svn_config_dir] value=$source_data.svn_config_dir}
          <p class="details">{lang}Leave empty to use system default{/lang}</p>
        {/wrap}
      </div>
      
      {wrap_buttons}
        {submit}Submit{/submit}
      {/wrap_buttons}
    {/form}
