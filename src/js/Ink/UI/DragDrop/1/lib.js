
Ink.createModule('Ink.UI.DragDrop', 1, ['Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1', 'Ink.Util.Array_1', 'Ink.UI.Common_1'], function(InkElement, InkEvent, InkCss, InkArray, UICommon){
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

            this._draggedElm = null;
            this._clonedElm = null;
            this._placeholderElm = null;

            this._mouseDelta = [0, 0];

            this._addEvents();
        },

        _addEvents: function() {
            InkEvent.on(this._element, 'mousedown touchstart', Ink.bindEvent(this._onMouseDown, this));
        },

        _onMouseDown: function(event) {
            var tgt = InkEvent.element(event);

            var draggedElm = InkElement.findUpwardsBySelector(tgt, this._options.dragItem);

            var elmIgnoreDraggable = InkElement.findUpwardsBySelector(tgt, this._options.ignoreDrag);

            if(draggedElm && !elmIgnoreDraggable) {

                InkEvent.stopDefault(event);

                // has handler
                var handleElm = Ink.s(this._options.dragHandle, draggedElm);
                if(handleElm && InkElement.findUpwardsBySelector(tgt, this._options.dragHandle)) {
                    this._dragActive = true;
                } else if (!handleElm) {
                    this._dragActive = true;
                }

                if (this._dragActive) {
                    this._startDrag(event, draggedElm);
                }
            }
        },

        _startDrag: function(event, draggedElm) {
            // TODO rename
            this._clonedElm = draggedElm.cloneNode(true);
            this._placeholderElm = draggedElm.cloneNode(false);

            InkCss.addClassName(this._clonedElm, this._options.classDraggableCloned);
            this._clonedElm.removeAttribute('id');

            InkCss.addClassName(this._placeholderElm, this._options.classPlaceholder);
            this._placeholderElm.removeAttribute('id');

            var rect = draggedElm.getBoundingClientRect();
            var dragElmDims = [
                rect.right - rect.left,
                rect.bottom - rect.top
            ];

            this._clonedElm.style.width = dragElmDims[0] + 'px';
            this._clonedElm.style.height = dragElmDims[1] + 'px';

            this._placeholderElm.style.width = dragElmDims[0] + 'px';
            this._placeholderElm.style.height = dragElmDims[1] + 'px';
            this._placeholderElm.style.visibility = 'hidden';

            // TODO goes in style
            this._clonedElm.style.position = 'fixed';
            this._clonedElm.style.zIndex = '1000';
            this._clonedElm.style.left = rect.left + 'px';
            this._clonedElm.style.top = rect.top + 'px';

            var mousePos = InkEvent.pointer(event);
            var dragElmPos = InkElement.offset(draggedElm);
            this._mouseDelta = [
                (mousePos.x - dragElmPos[0]),
                (mousePos.y - dragElmPos[1])
            ];

            this._clonedElm.style.opacity = '0.6';

            draggedElm.parentNode.insertBefore(this._clonedElm, draggedElm);

            // TODO rename
            this._draggedElm = draggedElm;

            draggedElm.parentNode.insertBefore(this._placeholderElm, draggedElm);
            InkCss.addClassName(draggedElm, 'hide-all');

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

            this._clonedElm.style.left =
                (mousePos.x - this._mouseDelta[0] - scrollLeft) + 'px';
            this._clonedElm.style.top =
                (mousePos.y - this._mouseDelta[1] - scrollTop) + 'px';

            var elUnderMouse = (function findElementUnderMouse(exceptFor) {
                // TODO take advantage of getElementsFromPoint when it comes out
                exceptFor.style.display = 'none';

                var ret = document.elementFromPoint(
                    mousePos.x - scrollLeft,
                    mousePos.y - scrollTop);

                exceptFor.style.display = '';

                return ret;
            }(this._clonedElm));

            var elmOverDroppable = InkElement.findUpwardsBySelector(elUnderMouse, this._options.dropItem);

            if (!elmOverDroppable && InkElement.isAncestorOf(this._element, elUnderMouse)) {
                elmOverDroppable = this._element;
            }

            if(elmOverDroppable && (InkElement.descendantOf(this._element, elmOverDroppable) || this._element === elmOverDroppable)) {
                var elmOver = InkElement.findUpwardsBySelector(elUnderMouse, this._options.dragItem);

                if(elmOver && !InkCss.hasClassName(elmOver, this._options.classDraggableCloned) && !InkCss.hasClassName(elmOver, this._options.classPlaceholder)) {
                    // The mouse cursor is over another drag-item
                    this._insertPlaceholder(elmOver);
                } else if (!Ink.s(this._options.dragItem, elmOverDroppable)) {
                    // The mouse cursor is over nothing in particular, but still inside a list of drag-items
                    elmOverDroppable.appendChild(this._placeholderElm);
                }
            } else {
                // The cursor is outside anything useful
            }
        },

        _onMouseUp: function() {
            if (!this._dragActive) { return; }

            // The actual dropping is just putting our *real* node where the placeholder once was.
            InkElement.insertBefore(this._draggedElm, this._placeholderElm);

            InkElement.remove(this._placeholderElm);
            InkElement.remove(this._clonedElm);

            InkCss.removeClassName(this._draggedElm, 'hide-all');

            this._placeholderElm = null;
            this._clonedElm = null;
            this._draggedElm = null;

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

            if (!InkArray.inArray(this._placeholderElm, InkElement.previousSiblings(elm))) {
                goesAfter = false;
            }

            if(goesAfter) {
                InkElement.insertAfter(this._placeholderElm, elm);
            } else {
                InkElement.insertBefore(this._placeholderElm, elm);
            }
        }
    };

    UICommon.createUIComponent(DragDrop);

    return DragDrop;
});

