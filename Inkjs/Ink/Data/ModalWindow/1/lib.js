/**
 * @module Ink.Data.ModalWindow
 * @desc Application main module class (to be inherited by apps)
 * @author hlima, ecunha, ttt  AT sapo.pt
 * @version 1
 */    

Ink.createModule('Ink.Data.ModalWindow', '1', ['Ink.Data.Binding_1', 'Ink.Dom.Selector_1','Ink.UI.Modal_1'], function(ko, Selector, Modal) {
    var Module = function(options) {
        var self = this;

        this.moduleName = 'Ink.Data.ModalWindow';
        this.modal = undefined;
        this.title = options.title;
        this.modalId = options.modalId;
        this.contentModule = options.contentModule;
        this.contentReady = false;
        this.modalEl = undefined;
        this.modalWidth = options.modalWidth || "80%";
        this.modalHeight = options.modalHeight || "80%";
        this.cancelVisible = ko.computed(function() {
           var cancelVisible = ko.unwrap(options.cancelVisible);
           return (typeof cancelVisible == 'boolean'?cancelVisible:true); 
        }); 
        this.confirmCaption = ko.computed(function() {
            return ko.unwrap(options.confirmCaption) || 'Confirm';
        });
        this.taskButtons = ko.observableArray();
        
        this.moduleData = {
            confirmHandler: undefined, 
            confirmDisabled: ko.observable(false), 
            params: undefined, 
            hide: this.hide.bind(this) 
        };
        
        this.confirmDisabled = ko.computed(function() {
           return ko.unwrap(self.moduleData.confirmDisabled()); 
        });

        this.notifyContentReady = function() {
            self.contentReady = true;
        }

        options.parent['modal'] = {
            show: function(params) {
                self.show(params);
            }
        }; 
    };

    Module.prototype.confirm = function() {
        if (this.moduleData.confirmHandler && (typeof this.moduleData.confirmHandler == 'function'))
            this.moduleData.confirmHandler();
    }
    
    Module.prototype.hide = function() {
        this.modal.dismiss();
    }
    
    Module.prototype._hideModal = function() {
        var self=this;
        var content;
        
        if (this.modal) {
            this.modal.destroy();
            content = Selector.select("#modalContent", this.modalEl)[0];
            ko.cleanNode(content);
            content.innerHTML = '';
            
            // If there's a focused element, let's loose it's focus
            document.activeElement.blur();
            
            // Hack to remove previous modal attributes
            window.setTimeout(function() {
                self.modalEl.removeAttribute('style');
                self.modalEl.parentNode.removeAttribute('data-instance');
            }, 400);
        }
    }

    Module.prototype.show = function(params) {
        var content;
        
        this.modalEl = Selector.select("#"+this.modalId+" .ink-modal")[0];
        this.modal = new Ink.UI.Modal(this.modalEl, {onDismiss: this._hideModal.bind(this)});
        this.modal._init();
        this.taskButtons(params.taskButtons || []);
        this.moduleData.params = params;
        this.moduleData.confirmHandler = undefined;

        content = Selector.select("#modalContent", this.modalEl)[0];

        ko.cleanNode(content);

        content.innerHTML = '<!--ko module: {name: contentModule, notifyReady: notifyContentReady, data: moduleData}--><!--/ko-->';

        ko.applyBindings(this, content);
        
        // Hack to fix the scroll bar to the top in Firefox
        content.style.overflowY = 'hidden';
        window.setTimeout(function() {
            content.scrollTop = 0;
            content.style.overflowY = 'auto';
        }, 250);
    }
    
    Module.prototype.handleTask = function(handler) {
        handler.call(this, this);
    }

    return Module;
});
