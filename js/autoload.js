( function(){
    var autoload = {
        'DatePicker_1': '.ink-datepicker',
        'Gallery_1': 'ul.ink-gallery-source',
        'Modal_1': '.ink-modal',
        'ProgressBar_1': '.ink-progress-bar',
        'SortableList_1': '.ink-sortable-list',
        'Spy_1': '*[data-spy="true"]',
        'Sticky_1': '.ink-navigation.sticky',
        'Table_1': '.ink-table',
        'Tabs_1': '.ink-tabs',
        'TreeView_1': '.ink-tree-view',
        'Toggle_1': '.ink-toggle,.toggle',
        'Tooltip_1': '.ink-tooltip,.tooltip'
    };

    Ink.requireModules(['Ink.Dom.Selector_1', 'Ink.Dom.Loaded_1', 'Ink.UI.SmoothScroller_1', 'Ink.UI.Close_1'],
        function( Selector, Loaded, Scroller, Close ){

        Loaded.run(function(){
            for( var mod in autoload ) if (autoload.hasOwnProperty(mod)) {
                (function () {
                    // below variable needs closure
                    var elements = Selector.select( autoload[mod] );
                    if( elements.length ){
                        Ink.requireModules( ['Ink.UI.' + mod ], function (Component) {
                            for (var i = 0, len = elements.length; i < len; i++) {
                                new Component(elements[i]);
                            }
                        });
                    }
                }());
            }
            Scroller.init();
            new Close();
        });
    });
})();
