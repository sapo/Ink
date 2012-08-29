{wrap field=name}
  {label for=tax_rateName required=yes}Name{/label}
  {text_field name=tax_rate[name] value=$tax_rate_data.name id=tax_rateName class=required}
{/wrap}

{wrap field=percentage}
  {label for=tax_ratePercentage}Tax Percentage {/label}
  {text_field name=tax_rate[percentage] value=$tax_rate_data.percentage id=tax_ratePercentage class=short} %
{/wrap}