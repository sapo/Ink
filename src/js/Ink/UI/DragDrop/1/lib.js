
Ink.createModule('Ink.UI.DragDrop', 1, ['Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1', 'Ink.UI.Common_1'], function(InkElement, InkEvent, InkCss, UICommon){
    'use strict';

    function DragDrop() {
        UICommon.BaseUIComponent.apply(this, arguments);
    }

    DragDrop._name = 'DragDrop_1';

    DragDrop._optionDefinition = {
        // dragdropContainer: ['Element', '.dragdrop-container'], - is this._element
        dragItem:       ['String', '.drag-item'],
        dragHandle:     ['String', '.drag-handle'],
        dropItem:       ['String', '.drop-item'],
        ignoreDrag:     ['String', '.drag-ignore'],
        classDraggableCloned: ['String', 'drag-cloned-item'],
        classPlaceholder: ['String', 'drag-placeholder-item']
    };

    DragDrop.prototype = {
        _init: function() {
            this._dragActive = false;

            this._elmDraggable = null;
            this._elmDraggableCloned = null;
            this._elmPlaceholder = null;

            this._mouseDelta = [0, 0];

            this._addEvents();
        },

        _addEvents: function() {
            InkEvent.on(this._element, 'mousedown touchstart', Ink.bindEvent(this._onMouseDown, this));
        },

        _onMouseDown: function(event) {
            var tgt = InkEvent.element(event);

            var elmDraggable = InkElement.findUpwardsBySelector(tgt, this._options.dragItem);

            var elmIgnoreDraggable = InkElement.findUpwardsBySelector(tgt, this._options.ignoreDrag);

            if(elmDraggable && !elmIgnoreDraggable) {

                InkEvent.stopDefault(event);

                // has handler
                var handleElm = Ink.s(this._options.dragHandle, elmDraggable);
                if(handleElm && InkElement.findUpwardsBySelector(tgt, this._options.dragHandle)) {
                    this._dragActive = true;
                } else if (!handleElm) {
                    this._dragActive = true;
                }

                if (this._dragActive) {
                    this._startDrag(event, elmDraggable);
                }
            }
        },

        _startDrag: function(event, elmDraggable) {
            // TODO rename
            this._elmDraggableCloned = elmDraggable.cloneNode(true);
            this._elmPlaceholder = elmDraggable.cloneNode(false);

            InkCss.addClassName(this._elmDraggableCloned, this._options.classDraggableCloned);
            this._elmDraggableCloned.removeAttribute('id');

            InkCss.addClassName(this._elmPlaceholder, this._options.classPlaceholder);
            this._elmPlaceholder.removeAttribute('id');

            var rect = elmDraggable.getBoundingClientRect();
            var dragElmDims = [
                rect.right - rect.left,
                rect.bottom - rect.top
            ];
            var dragElmPos = InkElement.offset(elmDraggable);

            this._elmDraggableCloned.style.width = dragElmDims[0] + 'px';
            this._elmDraggableCloned.style.height = dragElmDims[1] + 'px';

            this._elmPlaceholder.style.width = dragElmDims[0] + 'px';
            this._elmPlaceholder.style.height = dragElmDims[1] + 'px';
            this._elmPlaceholder.style.visibility = 'hidden';

            // TODO goes in style
            this._elmDraggableCloned.style.position = 'fixed';
            this._elmDraggableCloned.style.zIndex = '1000';
            this._elmDraggableCloned.style.left = rect.left + 'px';
            this._elmDraggableCloned.style.top = rect.top + 'px';

            var mousePos = InkEvent.pointer(event);
            this._mouseDelta = [
                (mousePos.x - dragElmPos[0]),
                (mousePos.y - dragElmPos[1])
            ];

            this._elmDraggableCloned.style.opacity = '0.6';

            elmDraggable.parentNode.insertBefore(this._elmDraggableCloned, elmDraggable);

            // TODO rename
            this._elmDraggable = elmDraggable;

            elmDraggable.parentNode.insertBefore(this._elmPlaceholder, elmDraggable);
            InkCss.addClassName(elmDraggable, 'hide-all');

            InkEvent.on(document, 'mousemove.inkdraggable touchmove.inkdraggable',
                Ink.bindEvent(InkEvent.throttle(this._onMouseMove, 50), this));
            InkEvent.on(document, 'mouseup.inkdraggable touchend.inkdraggable',
                Ink.bindEvent(this._onMouseUp, this));
        },

        _onMouseMove: function(event) {
            if (!this._dragActive) { return; }

            InkEvent.stopDefault(event);

            var mousePos = InkEvent.pointer(event);

            var scrollLeft = InkElement.scrollWidth();
            var scrollTop = InkElement.scrollHeight();

            this._elmDraggableCloned.style.left =
                (mousePos.x - this._mouseDelta[0] - scrollLeft) + 'px';
            this._elmDraggableCloned.style.top =
                (mousePos.y - this._mouseDelta[1] - scrollTop) + 'px';

            var elUnderMouse = (function findElementUnderMouse(exceptFor) {
                // TODO take advantage of getElementsFromPoint when it comes out
                exceptFor.style.display = 'none';

                var ret = document.elementFromPoint(
                    mousePos.x - scrollLeft,
                    mousePos.y - scrollTop);

                exceptFor.style.display = '';

                return ret;
            }(this._elmDraggableCloned));

            var elmOverDroppable = InkElement.findUpwardsBySelector(elUnderMouse, this._options.dropItem);

            if(elmOverDroppable && (InkElement.descendantOf(this._element, elmOverDroppable) || this._element === elmOverDroppable)) {
                var elmOver = InkElement.findUpwardsBySelector(elUnderMouse, this._options.dragItem);

                if(elmOver && !InkCss.hasClassName(elmOver, this._options.classDraggableCloned) && !InkCss.hasClassName(elmOver, this._options.classPlaceholder)) {
                    // The mouse cursor is over another drag-item
                    this._insertPlaceholder(elmOver);
                } else if (!Ink.s('.drag-item', elmOverDroppable)) {
                    // The mouse cursor is over nothing in particular, but still inside a list of drag-items
                    elmOverDroppable.appendChild(this._elmPlaceholder);
                }
            } else {
                // The cursor is outside anything useful
            }
        },

        _onMouseUp: function() {
            if (!this._dragActive) { return; }

            // The actual dropping is just putting our *real* node where the placeholder once was.
            InkElement.insertBefore(this._elmDraggable, this._elmPlaceholder);

            InkElement.remove(this._elmPlaceholder);
            InkElement.remove(this._elmDraggableCloned);

            InkCss.removeClassName(this._elmDraggable, 'hide-all');

            this._elmPlaceholder = null;
            this._elmDraggableCloned = null;
            this._elmDraggable = null;

            InkEvent.off(document, '.inkdraggable');

            this._dragActive = false;
        },

        /**
         * Called when mouse has moved over a new element
         *
         * Given a competitor drag-item, it figures out
         * whether we want to put our placeholder *after* it or *before* it.
         *
         **/
        _insertPlaceholder: function(elm) {
            var goesAfter = true;

            if (InkElement.previousSiblings(elm).indexOf(this._elmPlaceholder) === -1) {
                goesAfter = false;
            }

            if(goesAfter) {
                InkElement.insertAfter(this._elmPlaceholder, elm);
            } else {
                InkElement.insertBefore(this._elmPlaceholder, elm);
            }
        }
    };

    UICommon.createUIComponent(DragDrop);

    return DragDrop;
});

