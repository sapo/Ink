{title}Update Profile{/title}
{add_bread_crumb}Update Profile{/add_bread_crumb}

<div id="edit_user_profile">
  {form action=$active_user->getEditProfileUrl() method=post autofocus=no}
    <div class="col">
      {wrap field=email}
        {label for=userEmail required=yes}Email{/label}
        {text_field name=user[email] value=$user_data.email id=userEmail class='required validate_email'}
      {/wrap}
    </div>
    
    <div class="clear"></div>
    
    <div class="col">
      {wrap field=first_name}
        {label for=userFirstName}First Name{/label}
        {text_field name=user[first_name] value=$user_data.first_name id=userFirstName}
      {/wrap}
    </div>
    
    <div class="col">
      {wrap field=last_name}
        {label for=userLastName}Last Name{/label}
        {text_field name=user[last_name] value=$user_data.last_name id=userLastName}
      {/wrap}
    </div>
    <div class="clear"></div>
    
    <div class="col">
      {wrap field=title}
        {label for=userTitle}Title{/label}
        {text_field name='user[title]' value=$user_data.title id=userTitle}
      {/wrap}
    </div>
    <div class="clear"></div>
    
    <h2 class="section_name"><span class="section_name_span">{lang}Contact{/lang}</span></h2>
    <div class="section_container">
      <div class="col">
      {wrap field=phone_work}
        {label for=userPhoneWork}Office Phone Number{/label}
        {text_field name='user[phone_work]' value=$user_data.phone_work id=userPhoneWork}
      {/wrap}
      </div>
      
      <div class="col">
      {wrap field=phone_mobile}
        {label for=userPhoneMobile}Mobile Phone Number{/label}
        {text_field name='user[phone_mobile]' value=$user_data.phone_mobile id=userPhoneMobile}
      {/wrap}
      </div>
      
      {wrap field=im}
        {label for=userIm}Instant Messenger{/label}
        {select_im_type name='user[im_type]' value=$user_data.im_type class=auto} {text_field name='user[im_value]' value=$user_data.im_value id=userIm}
      {/wrap}
    </div>
    
    {wrap_buttons}
    	{submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>