<?php
/**
 * Separated markup to be repeated in the different tabs
 * @var [type]
 */
// dockable
// collapsible
// close
$dockable_html = <<<HTML
<div class="ink-container">
    <nav class="ink-navigation ink-dockable">
        <ul class="menu horizontal blue ink-l100 ink-m100 ink-s100">
            <li class="active"><a class="home" href="#ui_home">Home</a></li>
            <li><a href="#gallery">Gallery</a></li>
            <li><a href="#modal">Modal</a></li>
            <li><a href="#table">Table</a></li>
            <li><a href="#tree_view">Tree View</a></li>
            <li><a href="#sortable_list">Sortable List</a></li>
            <li><a href="#date_picker">Date Picker</a></li>
            <li><a href="#tabs">Tabs</a></li>
            <li><a href="#formvalidator">Form Validator</a></li>
        </ul>
    </nav>
</div>
HTML;
$collapsible_html = <<<HTML
<div class="ink-container">
    <nav class="ink-navigation ink-dockable">
        <ul class="menu horizontal blue ink-l100 ink-m100 ink-s100">
            <li class="active"><a class="home" href="#ui_home">Home</a></li>
            <li><a href="#gallery">Gallery</a></li>
            <li><a href="#modal">Modal</a></li>
            <li><a href="#table">Table</a></li>
            <li><a href="#tree_view">Tree View</a></li>
            <li><a href="#sortable_list">Sortable List</a></li>
            <li><a href="#date_picker">Date Picker</a></li>
            <li><a href="#tabs">Tabs</a></li>
            <li><a href="#formvalidator">Form Validator</a></li>
        </ul>
    </nav>
</div>
HTML;
$close_html = <<<HTML
<div class="ink-container">
    <nav class="ink-navigation ink-dockable">
        <ul class="menu horizontal blue ink-l100 ink-m100 ink-s100">
            <li class="active"><a class="home" href="#ui_home">Home</a></li>
            <li><a href="#gallery">Gallery</a></li>
            <li><a href="#modal">Modal</a></li>
            <li><a href="#table">Table</a></li>
            <li><a href="#tree_view">Tree View</a></li>
            <li><a href="#sortable_list">Sortable List</a></li>
            <li><a href="#date_picker">Date Picker</a></li>
            <li><a href="#tabs">Tabs</a></li>
            <li><a href="#formvalidator">Form Validator</a></li>
        </ul>
    </nav>
</div>
HTML;

/**
 * Different vars for different layouts
 */
$dockable_js = <<<JS
<script type="text/javascript">
    var dockable = new SAPO.Ink.Dockable('.ink-dockable');
</script>
JS;
$collapsible_js = <<<JS
<script type="text/javascript">
    var dockable = new SAPO.Ink.Dockable('.ink-dockable');
</script>
JS;
$close_js = <<<JS
<script type="text/javascript">
    var dockable = new SAPO.Ink.Dockable('.ink-dockable');
</script>
JS;
?>
<div class="ink-section" id="behaviors">
    <div class="ink-row ink-vspace">
            <div class="ink-gutter">
        <div class="ink-l90">
                <h3>Behaviors</h3>
        </div>
        <div class="ink-l33">
                <h4>Dockable</h4>
                <p>
                    <i>Dockable</i> keeps your menus visible while scrolling through the page.
                </p>
                <p>
                    Activate it by using the "ink-dockable" class. A working example of dockable is the submenu that keeps following your progress on this page.
                </p>
                <p>
                    Check the <a target="_blank" href="http://js.sapo.pt/SAPO/Ink/Dockable/doc.html">technical documentation</a> for more details.</p>
                </p>
        </div>

        <div class="ink-l33">
                <h4>Collapsible</h4>
                <p>
                    <i>Collapsible</i> turns your horizontal menus into vertical ones depending on the screen width. It gives you the possibility to collapse/expand your menus.
                </p>
                <p>
                    Activate it by using the "ink-collapsible" class. A working example of collapsible is the top most menu.
                </p>
                <p>
                    Check the <a target="_blank" href="http://js.sapo.pt/SAPO/Ink/Collapsible/doc.html">technical documentation</a> for more details.</p>
                </p>
        </div>

        <div class="ink-l33">
                <h4>Close</h4>
                <p>
                    When clicking an element with the "ink-close" class, the first "ink-alert" or "ink-alert-block" ancestor is removed from the document.
                </p>
                <p>
                    Activate it by using the "ink-close" class inside of an element with one of the previously mentioned classes.
                </p>
                <p>
                    Check the <a target="_blank" href="http://js.sapo.pt/SAPO/Ink/doc.html#Close">technical documentation</a> for more details.</p>
                </p>
        </div>
    </div>

    </div>
</div>
