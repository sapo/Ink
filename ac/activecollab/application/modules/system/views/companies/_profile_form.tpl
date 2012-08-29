<fieldset>
  <legend>{lang}Company{/lang}</legend>

  {wrap field=name}
    {label for=companyName required=yes}Name{/label}
    {text_field name='company[name]' value=$company_data.name id=companyName class=required}
  {/wrap}

  {wrap field=office_address}
    {label for=companyAddress}Address{/label}
    {textarea_field name='company[office_address]' id=companyAddress}{$company_data.office_address}{/textarea_field}
  {/wrap}
  
  {wrap field=office_phone}
    {label for=companyName}Phone Number{/label}
    {text_field name='company[office_phone]' value=$company_data.office_phone id=companyName}
  {/wrap}
  
  {wrap field=office_fax}
    {label for=companyFax}Fax Number{/label}
    {text_field name='company[office_fax]' value=$company_data.office_fax id=companyFax}
  {/wrap}
  
  {wrap field=office_homepage}
    {label for=companyHomepage}Homepage{/label}
    {text_field name='company[office_homepage]' value=$company_data.office_homepage id=companyHomepage}
  {/wrap}
</fieldset>