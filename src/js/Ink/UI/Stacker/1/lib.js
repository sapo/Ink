Ink.createModule('Ink.UI.Stacker', 1, ['Ink.UI.Common_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1'], function(InkUICommon, InkEvent, InkElement) {
    'use strict';

var Stacker = function(selector, options) {
    this._init(selector, options);
};

Stacker.prototype = {

    _init: function(selector)
    {
        /* globals console */
        this._rootElm = InkUICommon.elsOrSelector(selector, 'stacker root element')[0] || null;
        if(this._rootElm === null) {
            if(typeof console !== 'undefined') {
                console.warn('No root element');
            }
        }

        this._options = Ink.extendObj({
                    column: '.stacker-column',
                    item: '.stacker-item',
                    largeMax: Number.MAX_VALUE,
                    largeMin: 961,
                    mediumMax: 960,
                    mediumMin: 651,
                    smallMax: 650,
                    smallMin: 0,
                    largeCols: 3,
                    mediumCols: 2,
                    smallCols: 1,
                    customBreakPoints: false, // Must be: {xlarge: {max: 9999, min: 1281, cols: 5}, large:{max:1280, min:1001, cols:4} medium:{max:1000, min:801,cols:3}, ...etc..}
                    isOrdered: true,
                    onRunCallback: false,
                    onResizeCallback: false,
                    onAPIReloadCallback: false,
                    _debug: false
                }, arguments[1] || {}, InkElement.data(this._rootElm));  


        this._aList = []; 

        this._curLayout = 'large';
        this._runFirstTime = false;

        this._getPageItemsToList();

        if(this._canApplyLayoutChange() || !this._runFirstTime) {
            this._runFirstTime = true;
            this._applyLayoutChange();
            if(typeof(this._options.onRunCallback) === 'function') {
                this._options.onRunCallback(this._curLayout);
            }
        }
        this._addEvents();

    },

    addItem: function(item)
    {
        this._aList.push(item);
    },

    reloadItems: function()
    {
        this._applyLayoutChange();
        if(typeof(this._options.onAPIReloadCallback) === 'function') {
            this._options.onAPIReloadCallback(this._curLayout);
        }
    },

    _addEvents: function()
    {
        InkEvent.observe(window, 'resize', Ink.bindEvent(this._onResize, this));
    },

    _onResize: function()
    {
        if(this._canApplyLayoutChange()) {
            this._removeDomItems();
            this._applyLayoutChange();
            if(typeof(this._options.onResizeCallback) === 'function') {
                this._options.onResizeCallback(this._curLayout);
            }
        }
    },

    _setCurLayout: function()
    {
        var viewportWidth = InkElement.viewportWidth();
        if(typeof(this._options.customBreakPoints) === 'object') {
            for(var prop in this._options.customBreakPoints) {
                if(this._options.customBreakPoints.hasOwnProperty(prop)) {
                    if(viewportWidth >= Number(this._options.customBreakPoints[prop].min) && viewportWidth <= Number(this._options.customBreakPoints[prop].max) && this._curLayout !== prop) {
                        this._curLayout = prop;
                        return;
                    } 
                }
            }
        } else {
            if(viewportWidth <= Number(this._options.largeMax) && viewportWidth >= Number(this._options.largeMin) && this._curLayout !== 'large') {
                this._curLayout = 'large';
            } else if(viewportWidth >= Number(this._options.mediumMin) && viewportWidth <= Number(this._options.mediumMax) && this._curLayout !== 'medium') {
                this._curLayout = 'medium';
            } else if(viewportWidth >= Number(this._options.smallMin) && viewportWidth <= Number(this._options.smallMax) && this._curLayout !== 'small') {
                this._curLayout = 'small';
            }
        }
    },

    _getColumnsToShow: function()
    {
        if(typeof(this._options.customBreakPoints) === 'object') {
            return Number(this._options.customBreakPoints[this._curLayout].cols);
        } else {
            return Number(this._options[this._curLayout+'Cols']);
        }
    },

    _canApplyLayoutChange: function()
    {
        var curLayout = this._curLayout;
        this._setCurLayout();
        if(curLayout !== this._curLayout) {
            return true;
        }
        return false;
    },

    _getPageItemsToList: function()
    {
        this._aColumn = Ink.ss(this._options.column, this._rootElm);
        var totalCols = this._aColumn.length;
        var index = 0;
        if(totalCols > 0) {
            for(var i=0; i < this._aColumn.length; i++) {
                var aItems = Ink.ss(this._options.item, this._aColumn[i]);
                for(var j=0; j < aItems.length; j++) {
                    if(this._options.isOrdered) {
                        index = i + (j * totalCols);
                    }
                    this._aList[index] = aItems[j];
                    if(!this._options.isOrdered) {
                        index++;
                    }
                    //aItems[j].style.height = (100 + (Math.random() * 100))+'px';
                    aItems[j].parentNode.removeChild(aItems[j]);
                }
            }
            if(this._aList.length > 0 && this._options.isOrdered) {
                var aNewList = [];
                for(var ii=0; ii < this._aList.length; ii++) {
                    if(typeof(this._aList[ii]) !== 'undefined') {
                        aNewList.push(this._aList[ii]);
                    }
                }
                this._aList = aNewList;
            }
        }
    }, 

    _removeDomItems: function()
    {
        var totalCols = this._aColumn.length;
        if(totalCols > 0) {
            for(var i=0; i < totalCols; i++) {
                var aItems = Ink.ss(this._options.item, this._aColumn[i]);
                for(var j=aItems.length - 1; j >= 0; j--) {
                    aItems[j].parentNode.removeChild(aItems[j]);
                }
            }
        }
    },

    _applyLayoutChange: function()
    {
        var totalCols = this._getColumnsToShow();
        var totalItems = this._aList.length;
        var index = 0;
        var countCol = 0;
        if(totalCols > 0) {
            while(countCol < totalCols) {
                this._aColumn[countCol].appendChild(this._aList[index]);
                index++;
                countCol++;
                if(index === totalItems) {
                    return;
                }
                if(countCol === totalCols) {
                    countCol = 0;
                }
            }
        }
    },

    _debug: function() {}
};

return Stacker;



});
