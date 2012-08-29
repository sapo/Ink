{form action="?route=quick_search" method=post id="quick_search_form"}
  <input type="text" name="search_for" id="quick_search_input" /> <input type="image" src="{image_url name=search_small.gif}" id="quick_search_button" class="auto" /> <img src="{image_url name=indicator.gif}" alt="Working" id="quick_search_indicator" style="display: none" />
  <input type="hidden" name="search_type" value="in_projects" id="quick_search_type" />
  <ul>
    <li id="search_in_projects" class="selected">{lang}In Projects{/lang}</li>
    <li id="search_for_people">{lang}For Users{/lang}</li>
    <li id="search_for_projects">{lang}For Projects{/lang}</li>
  </ul>
  <div id="quick_search_results"></div>
{/form}
<script type="text/javascript">
  App.QuickSearch.init();
</script>