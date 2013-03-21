if( !!SAPO && ("Dom" in SAPO) && ("Loaded" in SAPO.Dom) ){

    ( function(){
        var autoload = {


            /***************************
             * Modal                   
             ***************************/
            'Modal': '.ink-modal',

            /***************************
             * Table                   
             ***************************/
            // 'Table': '.ink-table'

            /***************************
             * TreeView                   
             ***************************/
            'TreeView': '.ink-tree-view',

            /***************************
             * SortableList                   
             ***************************/
            'SortableList': '.ink-sortable-list',

            /***************************
             * DatePicker                   
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