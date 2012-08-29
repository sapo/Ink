{wrap field=name}
  {label for=languageName required=yes}Name{/label}
  {text_field name='language[name]' value=$language_data.name id=languageName class=required}
{/wrap}

{wrap field=type}
  {label for=languageLocale required=yes}Locale{/label}
  {text_field name='language[locale]' value=$language_data.locale id=languageLocale class=required}
{/wrap}