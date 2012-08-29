{title}Modify Company Identity{/title}
{add_bread_crumb}Modify{/add_bread_crumb}

{form action=$modify_company_details_url method=POST enctype="multipart/form-data"}
  <div class="col_wide" id="company_logo">
    {if $company_logo_url}
      {wrap field=overwrite_old}
        {label}Current Logo{/label}
          <p>
            <img src="{$company_logo_url}" alt="logo" {if $scale_image}width='420'{/if} class='company_logo' />
            {if $scale_image}
              <br /><span class="details">{lang}Image is scaled for display purposes{/lang}</span>
            {/if}
          </p>
          {checkbox_field name='overwite_old' value=$comany_data.overwrite_old id=overwrite_old}
          {label for=overwrite_old class=inline}Upload new logo{/label}
      {/wrap}
    {/if}

    <div id="new_logo">
      {wrap field=company_logo}
        {label for=company_logo}New Company logo:{/label}
        {file_field name='company_logo' id=company_logo class='long'}
      {/wrap}
    </div>
    
    {literal}
      <script type="text/javascript">
        $('#overwrite_old').change(function () {
          if ($(this).is(':checked')) {
            $('#new_logo').show();
          } else {
            $('#new_logo').hide();
          } // if          
        });
        
        $('#overwrite_old').change();
      </script>
    {/literal}
  </div>
  <div class="col_wide">
    {wrap field=company_name}
      {label for=company_name required=yes}Company Name{/label}
      {text_field name='company[name]' value=$company_data.name id=company_name class='long required'}
    {/wrap}
    {wrap field=company_details}
      {label for=company_details required=yes}Company Details{/label}
      {textarea_field name='company[details]' id='company_details' class='long invoicing_company_address required'}{$company_data.details}{/textarea_field}
      <p class="details boxless">{lang}Additional information you want to be displayed in invoice header (address, bank account number etc){/lang}</p>
    {/wrap}
  </div>
  <div class="clear"></div>
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}
