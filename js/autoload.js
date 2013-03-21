if( !!SAPO && ("Dom" in SAPO) && ("Loaded" in SAPO.Dom) ){

    ( function(){
        var autoload = {


            /***************************
             * Modal - default css selector is .ink-modal                   
             ***************************/
            'Modal': '.ink-modal',

            /***************************
             * Table - default css selector is .ink-table
             ***************************/
            // 'Table': '.ink-table'

            /***************************
             * TreeView - default css selector is .ink-tree-view
             ***************************/
            'TreeView': '.ink-tree-view',

            /***************************
             * SortableList - default css selector is .ink-sortable-list
             ***************************/
            'SortableList': '.ink-sortable-list',

            /***************************
             * DatePicker - default css selector is .ink-datepicker                  
             ***************************/
            'DatePicker': '.ink-datepicker'



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