{wrap field=name}
  {label for=currencyName required=yes}Name{/label}
  {text_field name=currency[name] value=$currency_data.name id=currencyName class=required}
{/wrap}

{wrap field=code}
  {label for=currencyCode required=yes}Code{/label}
  {text_field name=currency[code] value=$currency_data.code id=currencyCode class=required}
{/wrap}

{wrap field=default_rate}
  {label for=currencyDefaultRate}Default Hourly Rate{/label}
  {text_field name=currency[default_rate] value=$currency_data.default_rate id=currencyDefaultRate class=short}
  <p class="details">{lang}Default hourly rate for this currency. It can be overriden in invoices{/lang}.</p>
{/wrap}