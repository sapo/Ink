/**
 * @module Ink.Data.AutoComplete
 * @desc AutoComplete widget
 * @author hlima, ecunha, ttt  AT sapo.pt
 * @version 1
 */

Ink.createModule('Ink.Data.AutoComplete', '1', ['Ink.Data.Binding_1', 'Ink.Dom.Event_1', 'Ink.Dom.Element_1', 'Ink.Dom.Selector_1'], function(ko, inkEvt, inkEl, inkSel) {
    /*
     * Ink + Knockout autoComplete binding 
     * 
     */
    ko.bindingHandlers.autoComplete = {
            
        /*
         * Knockout custom binding init
         * 
         */
        init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
            var unwrap = ko.utils.unwrapObservable;
            var dataSource = valueAccessor();
            var binding = allBindingsAccessor();
            var valueProp = unwrap(binding.optionsValue);
            var labelProp = unwrap(binding.optionsText) || valueProp;
            var displayElement;
            var displayInput;
            var displayOptions;
            var displayButton;
            var options = {minLength: 0};
            var modelValue;
            var handleValueChange;

            if (binding.autoCompleteOptions) {
                options = Ink.extendObj(options, binding.autoCompleteOptions);
            }

            //Create a new input to be the autocomplete so that the label shows
            // also hide the original control since it will be used for the value binding
            element.style.display = 'none';
            displayElement = inkEl.htmlToFragment('<div style="overflow: visible" class="auto-complete control-group '+ element.getAttribute('class') +
            		'"><div class="control append-button"><span><input placeholder="' + (element.getAttribute('placeholder') || '') + 
            		'" type="text"></input></span><button class="ink-button"><i class="icon-remove"></i></button></div><div class="auto-complete-options"></div></div>').firstChild;
            
            element.parentNode.insertBefore(displayElement, element.nextSibling);
            
            displayInput = Ink.s('input', displayElement);
            displayButton = Ink.s('button', displayElement);
            displayOptions = Ink.s('.auto-complete-options', displayElement);
            
            //handle value changing
            modelValue = binding.value;

            // Reset button click handler
            inkEvt.observe(displayButton, 'click', function() {
                ko.bindingHandlers.autoComplete._updateValueAndLabel(binding, modelValue, '', undefined, displayInput, element);
            });
            
            if (modelValue) {
                // Handle value changed in the ui (update model value)
                handleValueChange = function (event, ui) {
                    var labelToWrite = ui.item ? ui.item.label : null;
                    var valueToWrite = ui.item ? ui.item.value : null;
                    
                    //The Label and Value should not be null, if it is
                    // then they did not make a selection so do not update the 
                    // ko model                            
                    if (labelToWrite && valueToWrite) {
                        ko.bindingHandlers.autoComplete._updateValueAndLabel(binding, modelValue, labelToWrite, valueToWrite, displayInput, element);
                    } else { //They did not make a valid selection so change the autoComplete box back to the previous selection
                        var currentModelValue = unwrap(modelValue);
                        
                        //If the currentModelValue exists and is not nothing, then find out the display
                        // otherwise just blank it out since it is an invalid value
                        if (!currentModelValue)
                            displayInput.value = '';
                        else {
                            //Go through the source and find the id, and use its label to set the autocomplete
                            var selectedItem = ko.bindingHandlers.autoComplete._findSelectedItem(dataSource, binding, currentModelValue);           

                            //If we found the item then update the display
                            if (selectedItem) {
                                var displayText = labelProp ? unwrap(selectedItem[labelProp]) : unwrap(selectedItem).toString();
                                displayInput.value = displayText;
                            } else { //if we did not find the item, then just blank it out, because it is an invalid value
                                displayInput.value = '';
                            }
                        }
                    }

                    return false;
                };
                options.change = handleValueChange;
            }

            
            // handle the choices being updated in a Dependant Observable (DO), so the update function doesn't 
            // have to do it each time the value is updated. Since we are passing the dataSource in DO, if it is
            // an observable, when you change the dataSource, the dependentObservable will be re-evaluated
            // and its subscribe event will fire allowing us to update the autocomplete datasource
            var mappedSource = ko.dependentObservable(function () {
                return ko.bindingHandlers.autoComplete._buildDataSource(dataSource, labelProp, valueProp);
            }, viewModel);
            
            //Subscribe to the knockout observable array to get new/removed items
            mappedSource.subscribe(function (newValue) {
                var ul = displayOptions.firstChild.firstChild;

                options.source = newValue;
                ko.bindingHandlers.autoComplete._buildOptions(ul, ko.bindingHandlers.autoComplete._buildDataSource(newValue, 'label', 'value'), displayInput.value);
            });

            options.source = mappedSource();

            ko.bindingHandlers.autoComplete._buildAutoComplete(displayElement, options);
        },

        
        /*
         * Knockout custom binding update
         * 
         */
        update: function (element, valueAccessor, allBindingsAccessor, viewModel) {
            //update value based on a model change
            var unwrap = ko.utils.unwrapObservable;
            var dataSource = valueAccessor();
            var binding = allBindingsAccessor();
            var valueProp = unwrap(binding.optionsValue);
            var labelProp = unwrap(binding.optionsText) || valueProp;
            var displayElement = element.nextSibling;
            var displayInput = Ink.s('input', displayElement);
            var modelValue = binding.value;
            var currentModelValue;
            var selectedItem;
            var displayText;
            
            if (modelValue) {
                currentModelValue = unwrap(modelValue);
                
                //Set the hidden box to be the same as the viewModels Bound property
                element.value = currentModelValue;

                // If the value is different from the label's, let's find the corresponding label 
                if (valueProp != labelProp) {
                    //Go through the source and find the id, and use its label to set the autocomplete
                    selectedItem = ko.bindingHandlers.autoComplete._findSelectedItem(dataSource, binding, currentModelValue);
                    
                    if (selectedItem) {
                        displayText = labelProp ? unwrap(selectedItem[labelProp]) : unwrap(selectedItem).toString();
                        displayInput.value = displayText;
                    } else {
                        displayInput.value = '';
                    }
                } else {
                    if (currentModelValue !== undefined) {
                        displayInput.value = currentModelValue;
                    }
                }
            }
        },
        
        
        /* 
         * 
         * Private aux functions
         * 
         */
        
        //Go through the source and find the id, and use its label to set the autocomplete
        _findSelectedItem: function (dataSource, binding, selectedValue) {
            var unwrap = ko.utils.unwrapObservable;
            var source = unwrap(dataSource);
            var valueProp = unwrap(binding.optionsValue);

            var selectedItem = ko.utils.arrayFirst(source, function (item) {
                if (unwrap(item[valueProp]) == selectedValue)
                    return true;
            }, this);

            return selectedItem;
        },
        
        _buildDataSource: function (dataSource, labelProp, valueProp) {
            var unwrap = ko.utils.unwrapObservable;
            var source = unwrap(dataSource);
            var mapped = ko.utils.arrayMap(source, function (item) {
                var result = {};
                result.label = labelProp ? unwrap(item[labelProp]) : unwrap(item).toString();  //show in pop-up choices
                result.value = valueProp ? unwrap(item[valueProp]) : unwrap(item).toString();  //value 
                return result;
            });
            return mapped;
        },
        
        _updateValueAndLabel: function (binding, modelValue, labelToWrite, valueToWrite, displayInput, element) {
            if (ko.isWriteableObservable(modelValue)) {
                //Since this is an observable, the update part will fire and select the 
                //  appropriate display values in the controls
                modelValue(valueToWrite);
            } else {  //write to non-observable
                if (binding['_ko_property_writers'] && binding['_ko_property_writers']['value']) {
                    binding['_ko_property_writers']['value'](valueToWrite);
                    //Because this is not an observable, we have to manually change the controls values
                    // since update will not do it for us (it will not fire since it is not observable)
                    displayInput.value = labelToWrite;
                    element.value = valueToWrite;
                }
            }
        },
        
        // Function to build the menu with the suggested options
        _buildOptions: function(ul, source, filter) {
            var child;
            var index;
            var label;
            var value;
            
            while (child=ul.firstChild) {
                ul.removeChild(child);
            }

            if (filter) {
                filter=filter.toLowerCase();
            }
            
            for (index=0; index<source.length; index++) {
                label = source[index].label;
                value = source[index].value;
                
                if (filter && (label.toLowerCase().indexOf(filter)==-1)) {
                    continue;
                }
                
                li = document.createElement('li');
                anchor = document.createElement('a');
                anchor.textContent=label;
                anchor.setAttribute('data-value', value);
                li.appendChild(anchor);
                
                ul.appendChild(li);
            }
        },
        
        // Function to transform the input into an autocomplete input
        _buildAutoComplete: function(displayElement, options) {
            var nav = document.createElement('nav');
            var ul = document.createElement('ul');
            //var li;
            //var anchor;
            var displayOptions;
            var displayInput;
            var activeItem=undefined;
            
            nav.setAttribute('class', 'ink-navigation');
            ul.setAttribute('class', 'menu vertical rounded shadowed white');
            nav.appendChild(ul);

            ko.bindingHandlers.autoComplete._buildOptions(ul, options.source);
            
            displayOptions = Ink.s('.auto-complete-options', displayElement);
            displayOptions.appendChild(nav);

            displayInput = Ink.s('input', displayElement);
            
            // Handle input focus
            inkEvt.observe(displayInput, 'focus', function() {
                ko.bindingHandlers.autoComplete._buildOptions(ul, options.source, displayInput.value);
                displayOptions.style.display = 'block';

                window.setTimeout(function() {
                    displayInput.select();
                }, 100);
            });

            // If the input looses focus lets invalidate the change
            inkEvt.observe(displayInput, 'blur', function() {
                window.setTimeout(function() {
                    displayOptions.style.display = 'none';
                    if (activeItem) {
                        activeItem.setAttribute('class', '');
                        activeItem = undefined;
                    }
                    if (options.change) {
                        options.change(event, {item: {}});
                    }
                }, 200);
            });

            // List option selected
            inkEvt.observe(displayOptions, 'click', function(event) {
                var target = inkEvt.element(event);
                var inputValue = target.textContent;
                
                displayInput.value = inputValue;
                ko.bindingHandlers.autoComplete._buildOptions(ul, options.source, inputValue);
                
                if (options.change) {
                    options.change(event, {item: {label: target.textContent, value: target.getAttribute('data-value')}});
                }
            }, true);
            
            // Key entered in input control
            inkEvt.observe(displayInput, 'keyup', function(event) {
               var inputValue;
               var keyCode;

               keyCode = event.keyCode;
               // Handle arrow keys and return
               if ( (keyCode == inkEvt.KEY_DOWN) || 
                    (keyCode == inkEvt.KEY_UP) || 
                    (keyCode == inkEvt.KEY_RETURN) || 
                    (keyCode == inkEvt.KEY_LEFT) || 
                    (keyCode == inkEvt.KEY_RIGHT) ) {
                   inkEvt.stop(event);
                   
                   if (keyCode == inkEvt.KEY_DOWN) {
                       if (!activeItem && ul.firstChild) {
                           activeItem = ul.firstChild;
                           activeItem.setAttribute('class', 'active');
                           return;
                       }
                       
                       if (activeItem && inkEl.nextElementSibling(activeItem)) {
                           activeItem.setAttribute('class', '');
                           activeItem = inkEl.nextElementSibling(activeItem);
                           activeItem.setAttribute('class', 'active');
                       }
                   }
                   
                   if (keyCode == inkEvt.KEY_UP) {
                       if (activeItem && (activeItem == ul.firstChild)) {
                           activeItem.setAttribute('class', '');
                           activeItem = undefined;
                           displayInput.select();
                           return;
                       }
                       
                       if (activeItem && inkEl.previousElementSibling(activeItem)) {
                           activeItem.setAttribute('class', '');
                           activeItem = inkEl.previousElementSibling(activeItem);
                           activeItem.setAttribute('class', 'active');
                       }
                   }
                   
                   if (keyCode == inkEvt.KEY_RETURN) {
                       if (activeItem) {
                           inputValue = activeItem.firstChild.textContent;
                           displayInput.value = inputValue;
                           ko.bindingHandlers.autoComplete._buildOptions(ul, options.source, inputValue);
                           displayInput.blur();

                           if (options.change) {
                               options.change(event, {item: {label: activeItem.firstChild.textContent, value: activeItem.firstChild.getAttribute('data-value')}});
                           }
                       }
                   }
                   
                   return false;
               }
               
               activeItem = undefined;
               ko.bindingHandlers.autoComplete._buildOptions(ul, options.source, displayInput.value);
            });                    
        }
    };
    
    return {};
});
