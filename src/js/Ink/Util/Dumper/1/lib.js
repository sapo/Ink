/**
 * Dump/Profiling Utilities
 * @module Ink.Util.Dumper_1
 * @version 1
 */

Ink.createModule('Ink.Util.Dumper', '1', [], function() {

    'use strict';

    /**
     * @namespace Ink.Util.Dumper_1 
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
         * @param {Mixed} param The thing to format.
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
         * Function that formats the parameter to display.
         *
         * @method _outputFormat
         * @param {Mixed} param The thing to format.
         * @param {Number} indent Indentation level.
         * @return {String} The parameter passed formatted to displat
         * @private
         * @static
         */
        _outputFormat: function(param, indent)
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
                        formated += this._tab + this._getTabs(indent) + '[' + key + '] => <b>'+_typeof+'</b>\n';
                        formated += this._tab + this._getTabs(indent) + '{\n';
                        formated += this._outputFormat(param[key], indent + 1) + this._tab + this._getTabs(indent) + '}\n';
                    } else if(param[key].constructor === Function) {
                        continue;
                    } else {
                        formated = formated + this._tab + this._getTabs(indent) + '[' + key + '] => ' + param[key] + '\n';
                    }
                } else {
                    formated = formated + this._tab + this._getTabs(indent) + '[' + key + '] => null \n';
                }
            }
            return formated;
        },

        /**
         * Prints variable structure.
         *
         * @method printDump
         * @param {Mixed}                 param       Variable to be dumped.
         * @param {DOMElement|String}   [target]    Element to print the dump on.
         * @return {void}
         * @public
         * @static
         * @sample Ink_Util_Dumper_printDump.html 
         */
        printDump: function(param, target)
        {
            /*jshint evil:true */
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
         * Get a variable's structure.
         *
         * @method returnDump
         * @param   {Mixed}       param   Variable to get the structure.
         * @return  {String}      The variable's structure.
         * @public
         * @static
         * @sample Ink_Util_Dumper_returnDump.html 
         */
        returnDump: function(param)
        {
            return this._formatParam(param);
        },

        /**
         * Alert a variable's structure.
         *
         * @method alertDump
         * @param {Mixed}     param     Variable to be dumped.
         * @return {void}
         * @public
         * @static
         * @sample Ink_Util_Dumper_alertDump.html 
         */
        alertDump: function(param)
        {
            window.alert(this._formatParam(param).replace(/(<b>)(Array|Object)(<\/b>)/g, "$2"));
        },

        /**
         * Prints the variable structure to a new window.
         *
         * @method windowDump
         * @param {Mixed}     param   Variable to be dumped.
         * @return {void}
         * @public
         * @static
         * @sample Ink_Util_Dumper_windowDump.html 
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
