<div id="empty_slate_system_roles" class="empty_slate">
  <h3>{lang}About Scheduled Tasks{/lang}</h3>
  
  <ul class="icon_list">
    <li>
      <img src="{image_url name=admin/modules.gif}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}Scheduled Tasks{/lang}</span>
      <span class="icon_list_description">{lang}Some activeCollab modules require to be called periodically in order to do something. For instance, Backup module requires to be called once a day in order to create a daily backup. Tasks that are executed in this way are usually utility tasks and do not require user interaction{/lang}.</span>
    </li>
    
    <li>
      <img src="{image_url name=admin/scheduled-tasks.gif}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}Execution Frequency{/lang}</span>
      <span class="icon_list_description">{lang}There are three type of scheduled events - events executed frequently (every 3 - 5 minutes), events executed once an hour and events executed once a day. These events need to be triggered from outside, by system utility used to periodically trigger and execute tasks{/lang}.</span>
    </li>
    
    <li>
      <img src="{image_url name=admin/cli.gif}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}Executing Scheduled Tasks{/lang}</span>
      <span class="icon_list_description">
        {lang}Scheduled tasks can be executed through command line by executing following commands{/lang}:
        <pre>php {scheduled_task_command task=frequently} &gt; /dev/null
php {scheduled_task_command task=hourly} &gt; /dev/null
php {scheduled_task_command task=daily} &gt; /dev/null</pre>
        {lang}or through web interface by sending HTTP request to event URL-s{/lang}:
        <pre>/usr/bin/curl -s "{scheduled_task_url task=frequently}" &gt; /dev/null
/usr/bin/curl -s "{scheduled_task_url task=hourly}" &gt; /dev/null
/usr/bin/curl -s "{scheduled_task_url task=daily}" &gt; /dev/null</pre>
        {lang}Commands listed above are just examples. Please consult your system administrator or hosting provider for exact location of PHP or curl executables and for assistance with getting these commands to execute properly on your server{/lang}.
      </span>
    </li>
    
    <li>
      <img src="{image_url name=admin/book.gif}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}More Info{/lang}</span>
      <span class="icon_list_description">You can read more about Scheduled Tasks and how they should be configured in Administrator's Guide.</span>
    </li>
  </ul>
</div>