/**
 * @module Ink.Util.Dumper_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Util.Dumper', '1', [], function() {

    'use strict';

    /**
     * Dump/Profiling Utilities
     *
     * @class Ink.Util.Dumper
     * @version 1
     * @static
     */
    var Dumper = {

        /**
         * Hex code for the 'tab'
         * 
         * @property _tab
         * @type {String}
         * @private
         * @readOnly
         * @static
         *
         */
        _tab: '\xA0\xA0\xA0\xA0',

        /**
         * Function that returns the argument passed formatted
         *
         * @method _formatParam
         * @param {Mixed} param
         * @return {String} The argument passed formatted
         * @private
         * @static
         */
        _formatParam: function(param)
        {
            var formated = '';

            switch(typeof(param)) {
                case 'string':
                    formated = '(string) '+param;
                    break;
                case 'number':
                    formated = '(number) '+param;
                    break;
                case 'boolean':
                    formated = '(boolean) '+param;
                    break;
                case 'object':
                    if(param !== null) {
                        if(param.constructor === Array) {
                            formated = 'Array \n{\n' + this._outputFormat(param, 0) + '\n}';
                        } else {
                            formated = 'Object \n{\n' + this._outputFormat(param, 0) + '\n}';
                        }
                    } else {
                        formated = 'null';
                    }
                    break;
                default:
                    formated = false;
            }

            return formated;
        },

        /**
         * Function that returns the tabs concatenated
         *
         * @method _getTabs
         * @param {Number} numberOfTabs Number of Tabs
         * @return {String} Tabs concatenated
         * @private
         * @static
         */
        _getTabs: function(numberOfTabs)
        {
            var tabs = '';
            for(var _i = 0; _i < numberOfTabs; _i++) {
                tabs += this._tab;
            }
            return tabs;
        },

        /**
         * Function that formats the parameter to display
         *
         * @method _outputFormat
         * @param {Any} param
         * @param {Number} dim
         * @return {String} The parameter passed formatted to displat
         * @private
         * @static
         */
        _outputFormat: function(param, dim)
        {
            var formated = '';
            //var _strVal = false;
            var _typeof = false;
            for(var key in param) {
                if(param[key] !== null) {
                    if(typeof(param[key]) === 'object' && (param[key].constructor === Array || param[key].constructor === Object)) {
                        if(param[key].constructor === Array) {
                            _typeof = 'Array';
                        } else if(param[key].constructor === Object) {
                            _typeof = 'Object';
                        }
                        formated += this._tab + this._getTabs(dim) + '[' + key + '] => <b>'+_typeof+'</b>\n';
                        formated += this._tab + this._getTabs(dim) + '{\n';
                        formated += this._outputFormat(param[key], dim + 1) + this._tab + this._getTabs(dim) + '}\n';
                    } else if(param[key].constructor === Function) {
                        continue;
                    } else {
                        formated = formated + this._tab + this._getTabs(dim) + '[' + key + '] => ' + param[key] + '\n';
                    }
                } else {
                    formated = formated + this._tab + this._getTabs(dim) + '[' + key + '] => null \n';
                }
            }
            return formated;
        },

        /**
         * Print variable structure. Can be passed an output target
         *
         * @method printDump
         * @param {Object|String|Boolean} param
         * @param {optional String|Object} target (can be an element ID or an element)
         * @public
         * @static
         */
        printDump: function(param, target)
        {
            if(!target || typeof(target) === 'undefined') {
                document.write('<pre>'+this._formatParam(param)+'</pre>');
            } else {
                if(typeof(target) === 'string') {
                    document.getElementById(target).innerHTML = '<pre>' + this._formatParam(param) + '</pre>';
                } else if(typeof(target) === 'object') {
                    target.innerHTML = '<pre>'+this._formatParam(param)+'</pre>';
                } else {
                    throw "TARGET must be an element or an element ID";
                }
            }
        },

        /**
         * Function that returns the variable's structure
         *
         * @method returnDump
         * @param {Object|String|Boolean} param
         * @return {String} The variable structure
         * @public
         * @static
         */
        returnDump: function(param)
        {
            return this._formatParam(param);
        },

        /**
         * Function that alerts the variable structure
         *
         * @method alertDump
         * @param {Object|String|Boolean} param
         * @public
         * @static
         */
        alertDump: function(param)
        {
            window.alert(this._formatParam(param).replace(/(<b>)(Array|Object)(<\/b>)/g, "$2"));
        },

        /**
         * Print to new window the variable structure
         *
         * @method windowDump
         * @param {Object|String|Boolean} param
         * @public
         * @static
         */
        windowDump: function(param)
        {
            var dumperwindow = 'dumperwindow_'+(Math.random() * 10000);
            var win = window.open('',
                dumperwindow,
                'width=400,height=300,left=50,top=50,status,menubar,scrollbars,resizable'
            );
            win.document.open();
            win.document.write('<pre>'+this._formatParam(param)+'</pre>');
            win.document.close();
            win.focus();
        }

    };

    return Dumper;

});
