<div class="wrapper">
  <div class="box">
    <div class="object_main_info">
       <h1 class="object_name">{$active_discussion->getName()|clean}</h1>
    </div>
    {if $page == 1}
      {mobile_access_object_properties object=$active_discussion show_body=true show_milestone=true show_milestone_day_info=true}
    {/if}
  </div>
   
  {mobile_access_object_comments object=$active_discussion user=$logged_user}
  {mobile_access_add_comment_form parent=$active_discussion comment_data=$comment_data}
</div>