
Ink.createModule('Ink.UI.DragDrop', 1, ['Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1', 'Ink.Util.Array_1', 'Ink.UI.Common_1'], function(InkElement, InkEvent, InkCss, InkArray, UICommon){
    'use strict';

    function findElementUnderMouse(opt) {
        // TODO take advantage of getElementsFromPoint when it comes out
        opt.exceptFor.style.display = 'none';

        var ret = document.elementFromPoint(
            opt.x,
            opt.y);

        opt.exceptFor.style.display = '';

        return ret;
    }

    function DragDrop() {
        UICommon.BaseUIComponent.apply(this, arguments);
    }

    DragDrop._name = 'DragDrop_1';

    DragDrop._optionDefinition = {
        // dragdropContainer: ['Element', '.dragdrop-container'], - is this._element
        dragItem:       ['String', '.drag-item'],
        dragHandle:     ['String', '.drag-handle'],
        dropZone:       ['String', '.drop-zone'],
        ignoreDrag:     ['String', '.drag-ignore'],
        draggedCloneClass: ['String', 'drag-cloned-item'],
        placeholderClass: ['String', 'drag-placeholder-item']
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

            InkCss.addClassName(this._clonedElm, this._options.draggedCloneClass);
            this._clonedElm.removeAttribute('id');

            InkCss.addClassName(this._placeholderElm, this._options.placeholderClass);
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


            var mouseMoveThrottled = InkEvent.throttle(this._onMouseMove, 50, {
                // Prevent the default of events
                preventDefault: true,
                bind: this
            });

            InkEvent.on(document, 'mousemove.inkdraggable touchmove.inkdraggable', mouseMoveThrottled);
            InkEvent.on(document, 'mouseup.inkdraggable touchend.inkdraggable',
                Ink.bindEvent(this._onMouseUp, this));
        },

        _onMouseMove: function(event) {
            if (!this._dragActive) { return; }

            var mousePos = InkEvent.pointer(event);

            var scrollLeft = InkElement.scrollWidth();
            var scrollTop = InkElement.scrollHeight();

            this._clonedElm.style.left =
                (mousePos.x - this._mouseDelta[0] - scrollLeft) + 'px';
            this._clonedElm.style.top =
                (mousePos.y - this._mouseDelta[1] - scrollTop) + 'px';

            var elUnderMouse = findElementUnderMouse({
                x: mousePos.x - scrollLeft,
                y: mousePos.y - scrollTop,
                exceptFor: this._clonedElm
            });

            var dropZoneUnderMouse =
                InkElement.findUpwardsBySelector(elUnderMouse, this._options.dropZone);

            if (!dropZoneUnderMouse &&
                    (InkElement.isAncestorOf(this._element, elUnderMouse) ||
                    this._element === elUnderMouse)) {
                dropZoneUnderMouse = this._element;
            }

            var isMyDropZone = InkElement.isAncestorOf(this._element, dropZoneUnderMouse) ||
                this._element === dropZoneUnderMouse;

            if(dropZoneUnderMouse && isMyDropZone) {
                var otherDragItem =
                    InkElement.findUpwardsBySelector(elUnderMouse, this._options.dragItem);

                if (otherDragItem && this._isDragItem(otherDragItem)) {
                    // The mouse cursor is over another drag-item
                    this._insertPlaceholder(otherDragItem);
                } else if (this._dropZoneIsEmpty(dropZoneUnderMouse)) {
                    // The mouse cursor is over an empty dropzone, so there is nowhere to put it "after" or "before"
                    dropZoneUnderMouse.appendChild(this._placeholderElm);
                }
            } else {
                // The cursor is outside anything useful
            }
        },

        /**
         * Returns whether a given .drag-item element is a plain old .drag-item element
         * and not one of the clones we're creating or the element we're really dragging.
         *
         * Used because the selector ".drag-item" finds these elements we don't consider drag-items
         *
         * @method _isDragItem
         **/
        _isDragItem: function (elm) {
             return (
                elm !== this._draggedElm &&
                elm !== this._placeholderElm &&
                elm !== this._clonedElm);
        },

        _dropZoneIsEmpty: function (dropZone) {
            // Find elements with the class .drag-item in the drop-zone
            var dragItems = Ink.ss(this._options.dragItem, dropZone);

            // Make sure none of these elements are actually the dragged element,
            // the placeholder, or the position:fixed clone.
            return !InkArray.some(dragItems, Ink.bindMethod(this, '_isDragItem'));
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
        },

        /**
         * Destroy your DragDrop, removing it from the DOM
         *
         * @method destroy
         **/
        destroy: function () {
            if (this._dragActive) {
                InkEvent.off(document, '.inkdraggable');
            }
            UICommon.destroyComponent.call(this);
        }
    };

    UICommon.createUIComponent(DragDrop);

    return DragDrop;
});

