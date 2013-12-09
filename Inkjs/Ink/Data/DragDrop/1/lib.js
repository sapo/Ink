/**
 * @module Ink.Data.DragDrop
 * @desc Drag & Drop bindings
 * @author hlima, ecunha, ttt  AT sapo.pt
 * @version 1
 */

Ink.createModule('Ink.Data.DragDrop', '1', ['Ink.Data.Binding_1', 'Ink.UI.Draggable_1', 'Ink.UI.Droppable_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Dom.Css_1', 'Ink.Dom.Selector_1'], function(ko, Draggable, Droppable, inkEl, inkEvt, inkCss, inkSel) {
    var unknownDropId = 0;
    var dataTransfer=undefined; // Holds the dragged data (can be a single object or an array)
    var dropSuccess = false;
    var selectedData = []; // Array of selected objects (multi drag)
    var lastSelectedContainer = undefined;
    
    /*
     * Droppable binding handler
     * 
     * Description: A panel that accepts draggable drops
     * 
     * Binding value: {Object}
     * Binding value properties: 
     * - {string} hoverClass: class name to add to the element when a draggable hovers over it 
     * - {function} dropHandler: function to execute when a draggable is dropped in this droppable (receives selectedData as a parameter)
     * 
     * Binding example: {hoverClass: 'my-drop-panel', dropHandler: handleDrop)}
     * 
     */
    ko.bindingHandlers.droppable = {
        _handleDrop: function(binding, draggable, droppable, evt) {
            var receiverEl;
            var dataIndex;
            
            if (draggable.parentNode) {
                draggable.parentNode.removeChild(draggable);
            }
            
            if (typeof binding.dropHandler == 'function') {
                receiverEl=document.elementFromPoint(evt.clientX, evt.clientY);
                receiverEl=inkEl.findUpwardsByClass(receiverEl, 'drag-enabled');

                dataIndex=(receiverEl?parseInt(receiverEl.getAttribute('data-index'), 10):undefined);
                binding.dropHandler(dataTransfer, dataIndex);
            }
            dropSuccess=true;
        }, 

        _clearHints: function() {
        	var hints;
        	var i;

        	hints = Ink.ss('.drop-place-hint-before');
        	for (i=0; i < hints.length; i++) {
        		inkCss.removeClassName(hints[i], 'drop-place-hint-before');
        	}
        	hints = Ink.ss('.drop-place-hint-after');
        	for (i=0; i < hints.length; i++) {
        		inkCss.removeClassName(hints[i], 'drop-place-hint-after');
        	}
        },
        
        _handleHover: function(draggable, droppable, evt) {
        	var receiverEl;

        	ko.bindingHandlers.droppable._clearHints();
        	receiverEl=document.elementFromPoint(evt.clientX, evt.clientY);
            receiverEl=inkEl.findUpwardsByClass(receiverEl, 'drag-enabled');

        	if (receiverEl) {
        		inkCss.addClassName(receiverEl, 'drop-place-hint-before');
        	} else {
        		receiverEl=Ink.ss('.drag-enabled', droppable);
        		if (receiverEl.length>0) {
            		if (inkCss.hasClassName(receiverEl[receiverEl.length-1], 'draggable-proxy')) {
            			receiverEl.pop();
            		} 
            		receiverEl=receiverEl[receiverEl.length-1];
            		inkCss.addClassName(receiverEl, 'drop-place-hint-after');
        		}
        	}
        }, 

        init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
            var attr;
            var binding = ko.unwrap(valueAccessor());
            var options = {hoverClass: 'drop-panel-active', onHover: ko.bindingHandlers.droppable._handleHover, onDrop: ko.bindingHandlers.droppable._handleDrop.bind(this, binding), onDropOut: ko.bindingHandlers.droppable._clearHints}; 
            
            if (typeof binding == 'object') {
                for (attr in binding) {
                    options[attr] = ko.unwrap(binding[attr]);
                }
            }
            
            inkCss.addClassName(element, 'disable-text-selection');
            
            // The droppable element must have a valid id
            element.id = element.id || 'droppable'+(unknownDropId++);
            Droppable.add(element, options);
        }
    };
    

    /*
     * Draggable container binding handler
     * 
     * Description: a panel that hosts a list of draggable objects (with multi-drag) support
     * 
     * Binding value: {Object}
     * Binding value properties: 
     * - {object} source: Array or ObservableArray that containes the draggable objects 
     * - {string} draggableTemplate: id of template to render the draggable
     * - {function} dropHandler: function to execute when a draggable from this container is dropped in a droppable (receives the selectedData as a parameter)
     * 
     * Binding example: {source: grayItems, draggableTemplate: 'veggieTemplate', dragOutHandler: onDragOut}
     * 
     */
    ko.bindingHandlers.draggableContainer = {
        _dragX: -1,
        _dragY: -1,
        _isMouseDown: false,
        _isDragging: false,
        _draggable: undefined,
        
        _handleDragStart: function(evt) {
            if (!ko.bindingHandlers.draggableContainer._isMouseDown) {
                ko.bindingHandlers.draggableContainer._isMouseDown = true;
                ko.bindingHandlers.draggableContainer._dragX = evt.screenX;
                ko.bindingHandlers.draggableContainer._dragY = evt.screenY;
            }
        },
        
        _handleDragEnd: function(evt) {
            ko.bindingHandlers.draggableContainer._isMouseDown = false;
            if (ko.bindingHandlers.draggableContainer._isDragging) {
                window.setTimeout(function() {
                    ko.bindingHandlers.draggableContainer._isDragging = false;
                }, 500);
            }
        },
        
        _clearSelection: function() {
            var selectedItems;
            var i;

            selectedItems = inkSel.select('.draggable-selected');
            for (i=0; i<selectedItems.length; i++) {
                inkCss.removeClassName(selectedItems[i], 'draggable-selected');
            }

            selectedData = [];
            lastSelectedContainer = undefined;
        },
        
        _handleDrop: function(binding, dragProxyElement) {
            window.setTimeout(function() {
                if (dropSuccess) {
                    if (typeof binding.dragOutHandler == 'function') {
                        binding.dragOutHandler(dataTransfer);
                    }
                } else {
                	if (dragProxyElement.parentNode) {
                        dragProxyElement.parentNode.removeChild(dragProxyElement);
                	}
                }
                ko.bindingHandlers.draggableContainer._clearSelection();
            }, 0);
        },
        
        _cloneEvent: function(evt) {
            return {
               target: evt.target,
               type: evt.type,
               button: evt.button,
               clientX: evt.clientX,
               clientY: evt.clientY,
               screenX: evt.screenX,
               screenY: evt.screenY,
               relatedTarget: evt.relatedTarget
            };
        },
        
        _cloneStyle: function(src, dst, cloneAll) {
            if (cloneAll) {
                dst.className           = src.className;
                dst.style.borderWidth   = '0';
                dst.style.padding       = '0';
            }
            
            dst.style.position      = 'absolute';
            dst.style.width         = inkEl.elementWidth(src) + 'px';
            dst.style.height        = inkEl.elementHeight(src) + 'px';
            dst.style.left          = inkEl.elementLeft(src) + 'px';
            dst.style.top           = inkEl.elementTop(src) + 'px';
            dst.style.cssFloat      = inkCss.getStyle(src, 'float');
            dst.style.display       = inkCss.getStyle(src, 'display');
        },        
        
        init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
            var binding = ko.unwrap(valueAccessor());
            var draggable;
            var draggableElement;
            var dragThreshold = (binding.dragThreshold || 4);

            var handleSelection = function(data, evt) {
                var draggableElement;
                var i;

                if (!ko.bindingHandlers.draggableContainer._isDragging) {
                    // If the user selects an item from a different container let's clear the old selection
                    if (lastSelectedContainer != element) {
                        ko.bindingHandlers.draggableContainer._clearSelection();
                        lastSelectedContainer = element;
                    }
                    
                    draggableElement = inkEl.findUpwardsByClass(evt.target, 'drag-enabled');
                    inkCss.toggleClassName(draggableElement, 'draggable-selected');
                    i = selectedData.indexOf(data);
                    if (i !=-1) {
                        selectedData.splice(i, 1);
                    } else {
                        selectedData.push(data);
                    }
                }
            };

            
            var handleDragMove = function(data, evt) {
                var draggableElement;
                var draggableProxy;
                var draggable;
                var clonedEvent;

                draggableElement = inkEl.findUpwardsByClass(evt.target, 'drag-enabled');
                
                if (ko.bindingHandlers.draggableContainer._isMouseDown && !ko.bindingHandlers.draggableContainer._isDragging) {
                    if ( (Math.abs(evt.screenX-ko.bindingHandlers.draggableContainer._dragX) > dragThreshold) ||
                         (Math.abs(evt.screenY-ko.bindingHandlers.draggableContainer._dragY) > dragThreshold) 
                       ) {
                        ko.bindingHandlers.draggableContainer._isDragging = true;

                        // If the dragged element isn't selected let's clear the selection
                        if (!inkCss.hasClassName(draggableElement, 'draggable-selected')) {
                            ko.bindingHandlers.draggableContainer._clearSelection();
                        }
                        
                        if (selectedData.length == 0) {
                            draggableProxy = draggableElement.cloneNode(true);
                            ko.bindingHandlers.draggableContainer._cloneStyle(draggableElement, draggableProxy, true);
                            dataTransfer = data;
                        } else {
                            draggableProxy = inkEl.htmlToFragment('<div>Multile elements selected</div>').firstChild;
                            ko.bindingHandlers.draggableContainer._cloneStyle(draggableElement, draggableProxy, false);
                            dataTransfer = selectedData;
                        }

                        inkCss.addClassName(draggableProxy, 'draggable-proxy');
                        
                        draggableProxy=element.appendChild(draggableProxy);
                        clonedEvent = ko.bindingHandlers.draggableContainer._cloneEvent(evt);
                        clonedEvent.target=draggableProxy;
                        
                        dropSuccess = false;
                        draggable=new Draggable(draggableProxy, {cursor: 'move', onEnd: ko.bindingHandlers.draggableContainer._handleDrop.bind(this, binding)});
                        draggable.handlers.start(clonedEvent);
                        ko.bindingHandlers.draggableContainer._draggable=draggable;
                    }
                } 
            };
            
            inkCss.addClassName(element, 'draggable-container');
            inkCss.addClassName(element, 'disable-text-selection');
            
            ko.computed(function() {
                var source = ko.unwrap(binding.source);
                var childElements;
                var i;
                
                childElements = inkSel.select('.drag-enabled', element);
                
                for (i=0; i < childElements.length; i++) {
                    inkEvt.stopObserving(childElements[i], 'click');
                    inkEvt.stopObserving(childElements[i], 'mousemove');
                    inkEvt.stopObserving(childElements[i], 'mousedown');
                    inkEvt.stopObserving(childElements[i], 'mouseup');
                    
                    childElements[i].parentNode.removeChild(childElements[i]);
                }
                
                for (i=0; i < source.length; i++) {
                    draggable = source[i];
                    
                    draggableElement = inkEl.htmlToFragment('<div data-index="'+i+'" class="drag-enabled disable-text-selection" style="cursor: move" data-bind="template: {name: \''+binding.draggableTemplate+'\'}"/>').firstChild;
                    draggableElement.dataTransfer = {data: draggable};
                    
                    ko.applyBindings(draggable, draggableElement);
                    
                    element.appendChild(draggableElement);
                    inkEvt.observe(draggableElement, 'click', handleSelection.bind(this, draggable));
                    inkEvt.observe(draggableElement, 'mousemove', handleDragMove.bind(this, draggable));

                    inkEvt.observe(draggableElement, 'mousedown', ko.bindingHandlers.draggableContainer._handleDragStart);
                    inkEvt.observe(document, 'mouseup', ko.bindingHandlers.draggableContainer._handleDragEnd);
                }
                
            });
            
            return {controlsDescendantBindings: true};
        }
    };
    
    return {};
});
