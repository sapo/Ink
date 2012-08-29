{title}Outgoing Email Settings{/title}
{add_bread_crumb}Outgoing Email Settings{/add_bread_crumb}

<div id="mailing_settings">
  {form action='?route=admin_settings_mailing' method=post id="mailing_settings_admin"}
    <div class="section_container">
      <div class="ctrlHolder">
        {label for=mailingType required=yes}Connection Type{/label}
        <select name="mailing[mailing]" id="mailingType">
          <option value="native" {if $mailing_data.mailing == 'native'}selected{/if}>Native</option>
          <option value="smtp" {if $mailing_data.mailing == 'smtp'}selected{/if}>SMTP</option>
        </select>
        <p class="details">{lang}Native mailing support uses your PHP setup to send emails. You can also use SMTP server to send emails.{/lang}</p>
      </div>
    </div>
    
    <div id="native_mailer_settings" style="display: none">
      <div class="section_container">
        {wrap field=mailing_native_options}
          {label for=mailingNativeOptions}Native Mailer Options{/label}
          {text_field name=mailing[mailing_native_options] value=$mailing_data.mailing_native_options id=mailingNativeOptions}
          <p class="details">{lang}Default value is "-oi -f %s". If you are <strong>experiencing problems</strong> with default value, try setting it empty value{/lang}</p>
        {/wrap}
      </div>
    </div>
    
    <div id="smtp_mailer_settings" style="display: none">
      <div class="section_container">
        <div class="col">
        {wrap field=mailing_smtp_host}
          {label for=mailingSmtpHost required=yes}SMTP host{/label}
          {text_field name=mailing[mailing_smtp_host] value=$mailing_data.mailing_smtp_host id=mailingSmtpHost}
        {/wrap}
        </div>
        
        <div class="col">
        {wrap field=mailing_smtp_port}
          {label for=mailingSmtpPort required=yes}SMTP port{/label}
          {text_field name=mailing[mailing_smtp_port] value=$mailing_data.mailing_smtp_port id=mailingSmtpPort class=short}
        {/wrap}
        </div>
        
        {wrap field=mailing_smtp_authenticate id=mailingAuthenticateRadioWrapper}
          {label for=mailingAuthenticate}SMTP Authentication{/label}
          {yes_no name=mailing[mailing_smtp_authenticate] value=$mailing_data.mailing_smtp_authenticate id=mailingAuthenticate}
        {/wrap}
        
        <div id="mailingAuthenticateWrapper" style="display: none">
          <div class="col">
          {wrap field=mailing_smtp_username}
            {label for=mailingUsername}Username{/label}
            {text_field name=mailing[mailing_smtp_username] value=$mailing_data.mailing_smtp_username id=mailingUsername}
          {/wrap}
          </div>
          
          <div class="col">
          {wrap field=mailing_smtp_password}
            {label for=mailingPassword}Password{/label}
            {password_field name=mailing[mailing_smtp_password] value=$mailing_data.mailing_smtp_password id=mailingPassword}
          {/wrap}
          </div>
          
          <div class="col">
          {wrap field=mailing_smtp_security}
            {label for=mailingType}Security{/label}
            <select name="mailing[mailing_smtp_security]" id="mailingSecurity">
              <option value="off" {if $mailing_data.mailing_smtp_security == 'off'}selected{/if}>Off</option>
              <option value="ssl" {if $mailing_data.mailing_smtp_security == 'ssl'}selected{/if}>SSL</option>
              <option value="tls" {if $mailing_data.mailing_smtp_security == 'tls'}selected{/if}>TLS</option>
            </select>
          {/wrap}
          </div>
        </div>
        
        <div class="clear"></div>
        <div id="test_connection" class="test_smtp_connection">
          <button type="button"><span><span>{lang}Test Connection{/lang}</span></span></button>
          <span class="test_connection_results">
            <img src="{image_url name=pending_indicator.gif}" alt='' />
            <span></span>
          </span>
        </div>
      </div>
    </div>
    
    <h2 class="section_name"><span class="section_name_span">{lang}Message Settings{/lang}</span></h2>
    
    <div id="mailing_bulk_settings">
      <div class="section_container">
        <div class="col">
          {wrap field=notifications_from_email}
            {label for=notificationsFromEmail}From Email{/label}
            {text_field name=mailing[notifications_from_email] value=$mailing_data.notifications_from_email id=notificationsFromEmail}
          {/wrap}
        </div>
        
        <div class="col">
          {wrap field=notifications_from_name}
            {label for=notificationsFromName}From Name{/label}
            {text_field name=mailing[notifications_from_name] value=$mailing_data.notifications_from_name id=notificationsFromName}
          {/wrap}
        </div>
        
        <div class="clear"></div>
        <p class="details">{lang email=$admin_email}Email clients will display this email address and name as sender. If this values are not set or valid, :email will be used{/lang}</p>
        
        {wrap field=bulk_options}
          <p><input type="checkbox" name="mailing[mailing_mark_as_bulk]" value="1" id="mailingMarkAsBulk" class="inline" {if $mailing_data.mailing_mark_as_bulk}checked="checked"{/if} />  {label for=mailingMarkAsBulk class=inline}Mark Notifications as Bulk Email (Recommended){/label}</p>
          <p><input type="checkbox" name="mailing[mailing_empty_return_path]" value="1" id="mailingEmptyReturnPath" class="inline" {if $mailing_data.mailing_empty_return_path}checked="checked"{/if} />  {label for=mailingEmptyReturnPath class=inline}Set Empty Return-Path{/label}</p>
        {/wrap}
        <p class="details">{lang}When messages are marked as bulk / auto-generated, email servers will not send automatic responses (such are Out of the Office messages) to them. Automatic responses are not desired if you are using Incoming Mail feature to capture responses to notifications as comments{/lang}</p>
      </div>
    </div>
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>

{empty_slate name=mailing module=system}