;(function(){
    var autoload = {
        /* Match module names to element classes (or more complex selectors)
         * which get the UI modules instantiated automatically. */
        'Animate_1'     : '.ink-animate',
        'Carousel_1'    : '.ink-carousel',
        'DatePicker_1'  : '.ink-datepicker',
        'Gallery_1'     : 'ul.ink-gallery-source',
        'Modal_1'       : '.ink-modal',
        'ProgressBar_1' : '.ink-progress-bar',
        'SortableList_1': '.ink-sortable-list',
        'Spy_1'         : '[data-spy="true"]',
        'Stacker_1'     : '.ink-stacker',
        'Sticky_1'      : '.ink-sticky, .sticky',
        'Table_1'       : '.ink-table',
        'Tabs_1'        : '.ink-tabs',
        'Toggle_1'      : '.ink-toggle, .toggle',
        'Tooltip_1'     : '.ink-tooltip, .tooltip',
        'TreeView_1'    : '.ink-tree-view'
    };

    Ink.requireModules(['Ink.Dom.Selector_1', 'Ink.Dom.Loaded_1', 'Ink.UI.SmoothScroller_1', 'Ink.UI.Close_1'],
        function( Selector, Loaded, Scroller, Close ){

        Loaded.run(function(){
            for( var mod in autoload ){
                if( !autoload.hasOwnProperty(mod) ){
                    continue;
                }
                // `elements` need to be in a closure because requireModules is async.
                findElements(mod);
            }
            Scroller.init();
            new Close();
        });

        function findElements(mod) {
            var elements = Selector.select( autoload[mod] );
            if( elements.length ){
                Ink.requireModules( ['Ink.UI.' + mod ], function( Component ) {
                    for (var i = 0, len = elements.length; i < len; i++) {
                        new Component(elements[i]);
                    }
                });
            }
        }
    });
}());
