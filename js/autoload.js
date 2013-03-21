if( !!SAPO && ("Dom" in SAPO) && ("Loaded" in SAPO.Dom) ){

    ( function(){
        var autoload = {


            /***************************
             * Modal - Default CSS selector is .ink-modal
             ***************************/
            'Modal': '.ink-modal',

            /***************************
             * Table - Default CSS selector is .ink-table
             ***************************/
            // 'Table': '.ink-table'

            /***************************
             * TreeView - Default CSS selector is .ink-tree-view
             ***************************/
            'TreeView': '.ink-tree-view',

            /***************************
             * SortableList - Default CSS selector is .ink-sortable-list
             ***************************/
            'SortableList': '.ink-sortable-list',

            /***************************
             * DatePicker - Default CSS selector is .ink-datepicker
             ***************************/
            'DatePicker': '.ink-datepicker',

            /***************************
             * Toggle - Default CSS selector is .toggle
             ***************************/
            'Toggle': '.toggle'



        };

        for( var module in autoload ){
            if( autoload.hasOwnProperty(module) ){
                SAPO.Dom.Loaded.run(function(){
                    SAPO.Dom.Selector.select( autoload[module] ).forEach(function( element ){ new SAPO.Ink[module]( element ); });
                });
            }
        }
    })();

}