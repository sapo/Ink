/**
 * @module Ink.UI.Close_1
 * @author inkdev AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.UI.Close', '1', ['Ink.Dom.Event_1','Ink.Dom.Css_1','Ink.Util.Array_1'], function(Event, Css, InkArray ) {
    'use strict';

    /**
     * Subscribes clicks on the document.body. If and only if you clicked on an element
     * having class "ink-close", will go up the DOM hierarchy looking for an element with any
     * of the following classes: "ink-alert", "ink-alert-block".
     * If it is found, it is removed from the DOM.
     * 
     * One should call close once per page (full page refresh).
     * 
     * @class Ink.UI.Close
     * @constructor
     * @version 1
     * @uses Ink.Dom.Event
     * @uses Ink.Dom.Css
     * @uses Ink.Util.Array
     * @example
     *     <script>
     *         Ink.requireModules(['Ink.UI.Close_1'],function( Close ){
     *             new Close();
     *         });
     *     </script>
     */
    var Close = function() {

        Event.observe(document.body, 'click', function(ev) {
            var el = Event.element(ev);
            if (!Css.hasClassName(el, 'ink-close') && !Css.hasClassName(el, 'ink-dismiss')) { return; }

            var classes;
            do { 
                if (!el.className) { continue; }
                classes = el.className.split(' ');
                if (!classes) { continue; }
                if ( InkArray.inArray('ink-alert',       classes) ||
                     InkArray.inArray('ink-alert-block', classes) ) { break; }
            } while ((el = el.parentNode));

            if (el) {
                Event.stop(ev);
                el.parentNode.removeChild(el);
            }
        });
    };

    return Close;

});
