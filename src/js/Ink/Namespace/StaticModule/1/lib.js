/* jshint unused:false */

/**
 * @module Ink.Namespace.StaticModule_1
 *
 * @author inkdev AT sapo.pt
 * @since April 2013
 * @version 1
 */



 /**
  * yuidoc comment syntax: http://yui.github.io/yuidoc/syntax/index.html
  */



Ink.createModule(
    'Ink.Namespace.StaticModule',         // full module name
    '1',                                  // module's version
    ['Ink.Dom.Event_1', 'Ink.Dom.Css_1'], // array of dependency modules
    function(Event, Css) {                // this fn will be called async with depencies as arguments

        'use strict';

        /**
         * This is a awesome set of methods to paint the sky.
         *
         * @namespace Ink.Namespace.StaticModule
         * @static
         */

        var StaticModule = {

            _privateMethod: function() {
                return 'foo';
            },

            /**
             * @property publicProperty
             */
            publicProperty: 'sky is blue',

            /**
             * Description of the method
             *
             * @method publicMethod
             * @param {String} name    bla ble bi
             * @param {Number} number  blo blu
             * @returns {String} the greet
             */
            publicMethod: function(name, number) {
                return ['Hello ', name, ', how are you doing? Your number is ', number, '.'].join('');
            }
        };

        return StaticModule; // this line is critical, otherwise nothing is bound to the module definition!
    }
);
