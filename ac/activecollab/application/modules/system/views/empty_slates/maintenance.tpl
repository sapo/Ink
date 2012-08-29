<div id="empty_slate_system_roles" class="empty_slate">
  <h3>{lang}About Maintenance Mode{/lang}</h3>
  
  <ul class="icon_list">
    <li>
      <img src="{image_url name=admin/whatis.gif}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}What is Maintenance Mode?{/lang}</span>
      <span class="icon_list_description">{lang}When system is maintenance mode, only administrators will be able to use it. All other users will receive "Service Unavailable" message. You can provide additional information to users by setting Maintenance Message value{/lang}.</span>
    </li>
    
    <li>
      <img src="{image_url name=admin/maintenance.gif}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}System Maintenance{/lang}</span>
      <span class="icon_list_description">{lang}System maintenance is special mode used when you want to bring down the entire system. This mode is useful when you are upgrading activeCollab for example. Because this mode is dependent on a single file, system will display a message you set while other files are being upgraded. This mode can be turned on by setting MAINTENANCE_MESSAGE option in config/config.php to value you want to display to users{/lang}.</span>
    </li>
  </ul>
</div>