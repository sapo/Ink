<div id="empty_slate_system_roles" class="empty_slate">
  <h3>{lang}About Mailing{/lang}</h3>
  
  <ul class="icon_list">
    <li>
      <img src="{image_url name=settings/mailing.gif module=system}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}Mailing Type{/lang}</span>
      <span class="icon_list_description">{lang}Native mailing uses the built-in <a href="http://www.php.net/manual/en/function.mail.php">mail()</a> function to send the emails. If PHP is properly configured, Native mailer works without any additional configuration. Alternatively, the system can also connect to a SMTP server to send the messages{/lang}.</span>
    </li>
    
    <li>
      <img src="{image_url name=admin/modules.gif}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}Troubleshooting{/lang}</span>
      <span class="icon_list_description">
        {lang}If you are having problems with getting activeCollab to send email messages, please review following notes{/lang}:
        <ol>
          <li>{lang}Email notifications are sent to all subscribers in their prefered language. Exception is person who does the action - even if that person is subscribed, he or she will not get a notification about what they just did because he or she is already aware of it. If you wish to test how notifications are working, you'll need an additional user account{/lang}.</li>
          <li>{lang}Many SMTP servers do not allow relaying of messages (i.e., emails sent to the server from external locations) when the sending email address is not on the same domain as the SMTP server. In this case, make sure that From Notifications address is of the same domain as SMTP server{/lang}.</li>
          <li>{lang}To see if activeCollab is sending message, turn <a href="http://www.vbsupport.org/forum/index.php">NulleD By FintMax - Not Support</a> on by setting DEBUG value to 2 in config/config.php and execute the action you think should produce an email. When done, open daily log from /logs folder and look for "mailing" section. This section contains information about mailer internal activity, whether it reached the server or not, which command and messages are sent etc{/lang}.</li>
        </ol>
      </span>
    </li>
  </ul>
</div>