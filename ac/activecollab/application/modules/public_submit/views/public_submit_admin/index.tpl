{title}Public Submit Settings{/title}
{add_bread_crumb}Public Submit{/add_bread_crumb}

<div id="public_submit_admin">
  <h2 class="section_name"><span class="section_name_span">{lang}Status{/lang}</span></h2>
  <div class="section_container content">
  <p>{lang}When enabled, Public Submit module will let people submit tickets through a form on your website without having to be a registered and logged in the system. This module is great for receiving support requests or getting feedback from users{/lang}.</p>
  <ul>
    {if $public_submit_enabled}
      <li>{lang }Public Submit is <strong>enabled</strong>{/lang}.</li>
      <li>{lang url=$public_submit_url}Submission form is located here: <a href=":url" target="_blank">:url</a>{/lang}.</li>
      
      {if instance_of($public_submit_project,'Project')}
        <li>{lang url=$public_submit_project->getOverviewUrl() project_name=$public_submit_project->getName()}After user submits data, a new ticket is created in <a href=":url">:project_name</a> project.{/lang}</li>
      {else}
        <li>{lang}You need to specify default project for newly created tickets first.{/lang}</li>
      {/if}
      
      {if $gd_not_loaded}
        <li>{lang gd_url='http://www.php.net/manual/en/ref.image.php'}GD Library with FreeType2 is required for captcha protection. <a href=":gd_url">Read more about GD Library</a>{/lang}</li>
      {else}
        {if $public_submit_captcha_enabled}
          <li>{lang}<a href="http://en.wikipedia.org/wiki/CAPTCHA" target="_blank">CAPTCHA</a> protection is <strong>enabled</strong> {/lang}.</li>
        {else}
          <li>{lang}<a href="http://en.wikipedia.org/wiki/CAPTCHA" target="_blank">CAPTCHA</a> protection is <strong>disabled</strong> {/lang}.</li>
        {/if}
      {/if}
    {else}
      <li>{lang }Public Submit is <strong>disabled</strong>{/lang}.</li>
    {/if}
  </ul>
  </div>

  <h2 class="section_name"><span class="section_name_span">{lang}Settings{/lang}</span></h2>
  <div class="section_container">
    {form action=$public_submit_settings_url method=POST}
      <div class="col">
        {wrap field=project_id}
          {label for=project_id required=yes}Project for tickets:{/label}
          {select_project user=$logged_user name=public_submit[project_id] value=$public_submit_data.project_id id=project_id show_all=true}
        {/wrap}
      </div>
      
      <div class="col">
        {wrap field=captcha}
          {label for=captcha required=yes}Enable captcha:{/label}
          {if !$gd_not_loaded}
            {yes_no name='public_submit[captcha]' id='captcha' value=$public_submit_data.captcha}
          {else}
            <span class="object_private">{lang gd_url='http://www.php.net/manual/en/ref.image.php'}GD Library is required for captcha protection. <a href=":gd_url">Read more about GD Library</a>{/lang}</span>
          {/if}
        {/wrap}
      </div>
      
      <div class="col">
        {wrap field=enabled}
          {label for=enabled required=yes}Enable public submit:{/label}
          {yes_no name='public_submit[enabled]' id='enabled' value=$public_submit_data.enabled}
        {/wrap}
      </div>
      <div class="clear"></div>
      
      {wrap_buttons}
        {submit}Submit{/submit}
      {/wrap_buttons}
    {/form}
  </div>
</div>