    {if $active_mailbox->isLoaded()}
    <h2 class="section_name"><span class="section_name_span">{lang}Mailbox Status{/lang}</span></h2>    
    <div class="section_container">
      <div class="col">
        {wrap field=enabled}
          {label for=enabled}Mailbox enabled{/label}
          {yes_no name="mailbox[enabled]" value=$mailbox_data.enabled id=enabled}
        {/wrap}
      </div>
    </div>
    {/if}
  

    <h2 class="section_name"><span class="section_name_span">{lang}Account Information{/lang}</span></h2>
    <div class="section_container">
      <div class="col">
      {wrap field=from_email}
        {label for=mailboxType required=yes}Account Email Address{/label}
        {text_field name="mailbox[from_email]" value=$mailbox_data.from_email id=fromEmail}
        <p class="details">Email address that will be checked with this mailbox.</p>
      {/wrap}
      </div>
      <div class="col">
      {wrap field=from_name}
        {label for=mailboxType}Account Name{/label}
        {text_field name="mailbox[from_name]" value=$mailbox_data.from_name id=fromName}
        <p class="details">Display name for this mailbox (personal reminder).</p>
      {/wrap}
      </div>
    </div>    

    <h2 class="section_name"><span class="section_name_span">{lang}Server Information{/lang}</span></h2>
    <div class="section_container">
      <div class="col">
      {wrap field=host}
        {label for=hostName required=yes}Incoming mail server{/label}
        {text_field name='mailbox[host]' value=$mailbox_data.host id=hostName class='required'}
      {/wrap}
      </div>
      
      <div class="col">
      {wrap field=username}
        {label for=username required=yes}Username{/label}
        {text_field name='mailbox[username]' value=$mailbox_data.username id=username class='required'}
      {/wrap}
      </div>
      
      <div class="col">
      {wrap field=password}
        {label for=password required=yes}Password{/label}
        {password_field name='mailbox[password]' value=$mailbox_data.password id=password class='required'}
      {/wrap}
      </div>
      
      <div class="col">
      {wrap field=type}
        {label for=mailboxType required=yes}Server Type{/label}
        {select_mailbox_type name="mailbox[type]" value=$mailbox_data.type id=mailboxType class='required'}
      {/wrap}
      </div>
      
      <div class="col">
      {wrap field=security}
        {label for=mailboxType required=yes}Server Security{/label}
        {select_mailbox_security name="mailbox[security]" value=$mailbox_data.security id=mailboxSecurity class='required'}
      {/wrap}
      </div>
      
      <div class="col">
      {wrap field=port}
        {label for=mailboxPort}Server Port{/label}
        {text_field name='mailbox[port]' value=$mailbox_data.port id=mailboxPort class=short}
      {/wrap}
      </div>
                        
      <div class="col">
      {wrap field=mailbox}
        {label for=mailboxName required=yes}Mailbox Name{/label}
        {text_field name='mailbox[mailbox]' value=$mailbox_data.mailbox id=mailboxName class='required'}
        <p class="details">This is mailbox name on your pop3/imap server. In most cases it should be left as default value ('INBOX') unless you want to check some other mailbox.</p>
      {/wrap}
      </div>
      
      <div id="test_connection">
        <button type="button"><span><span>{lang}Test Connection{/lang}</span></span></button>
        <span class="test_connection_results">
          <img src="{image_url name=pending_indicator.gif}" alt='' />
          <span></span>
        </span>
      </div>
    </div>
        
    <h2 class="section_name"><span class="section_name_span">{lang}Automated Action{/lang}</span></h2>
    <div class="section_container">
      <div class="col">
        {wrap field=object_type}
          {label for=objectType required=yes}Creates{/label}
          {select_incoming_mail_object_type name="mailbox[object_type]" value=$mailbox_data.object_type id=objectType class='required'}
        {/wrap}
      </div>
      
      <div class="col">
        {wrap field=project_id}
          {label for=project required=yes}In Project{/label}
          {select_project name="mailbox[project_id]" value=$mailbox_data.project_id user=$logged_user show_all=true}
        {/wrap}
      </div>
           
      <div class="col">
        {wrap field=accept_all_registered}
          {label for=accept_all_registered}Additional Permissions{/label}
          <div>{checkbox_field name=mailbox[accept_all_registered] checked=$mailbox_data.accept_all_registered id=accept_all_registered value=1} <label class="inline" for="accept_all_registered">Accept emails from <strong><u>all</u></strong> activeCollab users</label></div>
          <div>{checkbox_field name=mailbox[accept_anonymous] checked=$mailbox_data.accept_anonymous id=accept_anonymous value=1} <label class="inline" for="accept_anonymous">Accept emails from unregistered users</label></div>
        {/wrap}
      </div>      
    </div>