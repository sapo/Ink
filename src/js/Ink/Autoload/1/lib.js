/** 
 * @module Ink.Autoload
 * @version 1
 * Create Ink UI components easily
 */
Ink.createModule('Ink.Autoload', 1, ['Ink.Dom.Selector_1', 'Ink.Dom.Loaded_1', 'Ink.UI.SmoothScroller_1', 'Ink.UI.Close_1'], function( Selector, Loaded, Scroller, Close ){
    'use strict';

    /**
     * @namespace Ink.Autoload
     * @static
     */

    var el = document.createElement('div');
    // See if a selector is valid.
    function validSelector(sel) {
        try {
            Selector.select(sel, el);
        } catch(e) {
            Ink.error(e);
            return false;
        }
        return true;
    }

    var Autoload = {
        /**
         * Matches module names to default selectors.
         * 
         * @property selectors {Object}
         * @public
         **/
        selectors: {
            /* Match module names to element classes (or more complex selectors)
             * which get the UI modules instantiated automatically. */
            'Animate_1'     : '.ink-animate',
            'Carousel_1'    : '.ink-carousel',
            'DatePicker_1'  : '.ink-datepicker',
            'Dropdown_1'    : '.ink-dropdown',
            'Gallery_1'     : 'ul.ink-gallery-source',
            'Modal_1'       : '.ink-modal',
            'ProgressBar_1' : '.ink-progress-bar',
            'SortableList_1': '.ink-sortable-list',
            'Spy_1'         : '[data-spy="true"]',
            'Stacker_1'     : '.ink-stacker',
            'Sticky_1'      : '.ink-sticky, .sticky',
            'Table_1'       : '.ink-table',
            'Tabs_1'        : '.ink-tabs',
            'Toggle_1'      : '.ink-toggle, .toggle',
            'Tooltip_1'     : '.ink-tooltip, .tooltip',
            'TreeView_1'    : '.ink-tree-view'
        },
        defaultOptions: {},

        /**
         * Run Autoload on a specific element.
         *
         * Useful when you load something from AJAX and want it to have automatically loaded Ink modules.
         * @method run
         * @param {DOMElement} parentEl  
         * @param {Object}  [options] Options object, containing:
         * @param {Boolean} [options.createClose] Whether to create the Ink.UI.Close component. Defaults to `true`.
         * @param {Boolean} [options.createSmoothScroller] Whether to create the Scroller component. Defaults to `true`.
         * @public
         * @sample Autoload_1.html
         **/
        run: function (parentEl, options){
            options = Ink.extendObj({
                waitForDOMLoaded: false,
                createClose: false,
                createSmoothScroller: false,
                selectors: Autoload.selectors
            }, options || {});

            for(var mod in options.selectors) if (options.selectors.hasOwnProperty(mod)) {
                // `elements` need to be in a closure because requireModules is async.
                findElements(mod);
            }
            if (options.createClose !== false) {
                new Close();
            }
            if (options.createSmoothScroller !== false) {
                Scroller.init();
            }

            function findElements(mod) {
                var modName = 'Ink.UI.' + mod;
                var elements = Selector.select( options.selectors[mod], parentEl );
                if( elements.length ){
                    Ink.requireModules( [modName], function( Component ) {
                        for (var i = 0, len = elements.length; i < len; i++) {
                            new Component(elements[i], Autoload.defaultOptions[modName]);
                        }
                    });
                }
            }
        },
        /**
         * Add a new entry to be autoloaded.
         * @method add
         * @param moduleName {String}
         * @param selector   {String}
         */
        add: function (moduleName, selector) {
            if (!validSelector(selector)) { return false; }

            if (Autoload.selectors[moduleName]) {
                Autoload.selectors[moduleName] += ', ' + selector;
            } else {
                Autoload.selectors[moduleName] = selector;
            }
        },
        /**
         * Removes a module from autoload, making it not be automatically loaded.
         * @method remove
         * @param moduleName {String}
         **/
        remove: function (moduleName) {
            delete Autoload.selectors[moduleName];
        }
    };

    for (var k in Autoload.selectors) if (Autoload.selectors.hasOwnProperty(k)) {
        Autoload.defaultOptions[k] = {};
    }

    if (!window.INK_NO_AUTO_LOAD) {
        Loaded.run(function () {
            Autoload.run(document, {
                createSmoothScroller: true,
                createClose: true
            });
            Autoload.firstRunDone = true;
        });
    }

    return Autoload;
});

