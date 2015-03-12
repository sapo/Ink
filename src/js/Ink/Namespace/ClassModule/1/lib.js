/**
 * @module Ink.Namespace.ClassModule_1
 *
 * @author inkdev AT sapo.pt
 * @since April 2013
 * @version 1
 */
/* jshint unused:false */


 /**
  * yuidoc comment syntax: http://yui.github.io/yuidoc/syntax/index.html
  */



Ink.createModule(
    'Ink.Namespace.ClassModule',          // full module name
    '1',                                  // module's version
    ['Ink.Dom.Event_1', 'Ink.Dom.Css_1'], // array of dependency modules
    function(Event, Css) {                // this fn will be called async with depencies as arguments

        'use strict';

        /**
         * Use this to bake cakes.
         *
         * @class Ink.Namespace.ClassModule
         * @constructor
         * @param {Object} options
         * @param {String} [options.opt1] asda sdas d
         * @param {String} [options.opt2] asda sdas d
         */

        var ModuleName = function(options) {
            this._init(options);
        };

        ModuleName.prototype = {

            _init: function(options) {
                this._options = Ink.extendObj({
                    opt1: 'foo',
                    opt2: 'bar'
                }, options || {});

                this._stuff = false;

                this._privMethod1();
            },

            _privMethod1: function() {
                this._stuff = this._options.opt1;
            },

            /**
             * @method bake
             * @return {Number} a number between 0 and 1
             * @public
             */
            bake: function() {
                return Math.random();
            },

            /**
             * @method bake
             * @param {String} ingredient What to bake
             * @param {Number} time       How long to bake it
             * @return {void}
             * @public
             */
            cook: function(ingredient, time) {
                return this._privMethod2();
            }
        };

        return ModuleName; // this line is critical, otherwise nothing is bound to the module definition!
    }
);
