{title}Number Generator{/title}
{add_bread_crumb}Settings{/add_bread_crumb}

{form method=post}
  {wrap field=pattern}
    {label for=generatorPattern required=yes}Generator Pattern{/label}
    {text_field name=generator[pattern] value=$generator_data.pattern id=generatorPattern class='invoice_generator_pattern_input required validate_minlength 3'}
  {/wrap}
  
  {wrap field=preview}
    {label}Preview{/label}
    {text_field name=preview id=generatorPreview class='invoice_generator_preview_input medium' disabled=disabled}
  {/wrap}
  
  {wrap field=patterns_and_counters}
    {label}Available Counter and Variables{/label}
    <div class="generator_patterns_and_counters">
      <ul class="invoice_generator_variables">
        <li>
          <strong>Counters</strong>
        </li>
        <li>
          <a href="#">{$smarty.const.INVOICE_NUMBER_COUNTER_TOTAL}</a><br />
          {lang}Invoice number in total{/lang}
        </li>
        <li>
          <a href="#">{$smarty.const.INVOICE_NUMBER_COUNTER_YEAR}</a><br />
          {lang}Invoice number in current year{/lang}
        </li>
        <li>
          <a href="#">{$smarty.const.INVOICE_NUMBER_COUNTER_MONTH}</a><br />
          {lang}Invoice number in current month{/lang}
        </li>
      </ul>
      <ul class="invoice_generator_variables">
        <li>
          <strong>Variables</strong>
        </li>
        <li>
          <a href="#">{$smarty.const.INVOICE_VARIABLE_CURRENT_YEAR}</a><br />
          {lang}Current year in number format{/lang}
        </li>
        <li>
          <a href="#">{$smarty.const.INVOICE_VARIABLE_CURRENT_MONTH}</a><br />
          {lang}Current month in number format{/lang}
        </li>
        <li>
          <a href="#">{$smarty.const.INVOICE_VARIABLE_CURRENT_MONTH_SHORT}</a><br />
          {lang}Current month in short text format{/lang}
        </li>
        <li>
          <a href="#">{$smarty.const.INVOICE_VARIABLE_CURRENT_MONTH_LONG}</a><br />
          {lang}Current month in long text format{/lang}
        </li>
      </ul>
    </div>
  {/wrap}
  {wrap_buttons}
    {submit}Submit{/submit}
  {/wrap_buttons}
{/form}