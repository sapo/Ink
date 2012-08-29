<div class="wrapper">
  <div class="box">
    <div class="object_main_info">
       <h1 class="object_name">{$parent->getName()|clean}</h1>
    </div>
  </div>

  {mobile_access_add_comment_form parent=$parent comment_data=$comment_data}
  {mobile_access_object_comments object=$parent user=$logged_user}
</div>