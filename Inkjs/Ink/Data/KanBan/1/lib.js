/**
 * @module Ink.Data.KanBan
 * @desc KanBan widget
 * @author hlima, ecunha, ttt  AT sapo.pt
 * @version 1
 */    

Ink.createModule('Ink.Data.KanBan', '1', ['Ink.Data.Binding_1', 'Ink.Data.DragDrop_1'], function(ko) {
    var Module = function(options) {
        this.moduleName = 'Ink.Data.KanBan';
        this.sections = options.sections;
    };

    Module.prototype.dragOutHandler = function(source, data) {
        var i;
        var dataIndex;
        
        if (typeof data.length == 'undefined') {
            i=source.indexOf(data);
            if (i != -1) {
                source.splice(i, 1);
            }
        } else {
            for (dataIndex=0; dataIndex < data.length; dataIndex++) {
                i=source.indexOf(data[dataIndex]);
                if (i != -1) {
                    source.splice(i, 1);
                }
            }
        }
    };
    
    Module.prototype.dropHandler = function(source, data, index) {
    	var i;
        var oldItem = undefined;
        var newIndex;

        if (typeof data.length == 'undefined') {
        	data = [data];
        }
        
        if (index !== undefined) {
            oldItem = source()[index];

            // if the oldItem is equal to the dropped data item, then it's going to be removed
            // so, let's go to the next one
        	for (i=0; i < data.length; i++) {
            	if ( (data[i] === oldItem) && (++index<source().length) ) {
            		oldItem = source()[index];
            	}
        	}
            	
        	if (index==source().length) {
        		oldItem = undefined;
        	}
        }
        
        window.setTimeout(function() {
        	if (oldItem !== undefined) {
            	newIndex = source.indexOf(oldItem);
        	} else {
        		newIndex = source().length;
        	}
            
        	for (i=0; i < data.length; i++) {
        		source.splice(newIndex, 0, data[data.length-1-i]);
        	}
        }, 0);
    };
    
    return Module;
});
