
Ink.createModule('Ink.UI.AutoComplete', '1', ['Ink.UI.Common_1', 'Ink.Dom.Element_1', 'Ink.Dom.Event_1', 'Ink.Net.Ajax_1'], function (Common, InkElement, InkEvent, Ajax) {
'use strict';

/**
 * @module Ink.UI.AutoComplete_1
 */
function AutoComplete(elem, options) {
    this._init(elem, options);
}

AutoComplete.prototype = {
    /**
     * @class Ink.UI.AutoComplete_1
     * @constructor
     *
     * @param {String|DOMElement} elem String or DOMElement for the input field
     */
    _init: function(elem, options) {
        this._options = Ink.extendObj({
            inputField: elem || /* [todo] this is not an option */ false,
            target: false,
            suggestionsURI: false,
            classNameSelected:'selected',
            suggestionsObject: false,
            resultLimit: 10,
            minLength: 1,
            debug: false
        }, options || {});

        if (!(this._options.suggestionsURI || !this._options.suggestionsObject)) {
            Ink.error("É obrigatório especificar o endpoint ou o objecto global para carregamento das sugestões!");
            return;
        }

        this._element = Common.elOrSelector(elem);
        this._options.target = Common.elOrSelector(this._options.target);

        this._element = Ink.s(this._options.inputField);
        this.suggestPlaceElm = Ink.s(this._options.suggestPlace) || InkElement.create('div');

        this._addEvents();
    },

    _setElmVars: function() {
    },

    _addEvents: function() {
        this._handlers = {
            keyup: InkEvent.observe(this._element, 'keyup', Ink.bindEvent(this._onTypeInput, this)),
            focus: InkEvent.observe(this._element, 'focus', Ink.bindEvent(this._onFocusInput, this)),
            windowclick: InkEvent.observe(window, 'click', Ink.bindEvent(this._onClickWindow, this))
        }
    },

    _onTypeInput: function(e) {
        var keycode = e.keyCode;

        if(
                keycode != InkEvent.KEY_DOWN &&
                keycode != InkEvent.KEY_UP &&
                keycode != InkEvent.KEY_ESC &&
                keycode != InkEvent.KEY_TAB &&
                keycode != InkEvent.KEY_LEFT &&
                keycode != InkEvent.KEY_RIGHT
                ) {
            var value = this._getInputValue();

            if (value.length >= this._options.minLength) {
                // get suggestions based on name
                this.suggestPlaceElm.innerHTML = '';
                this._submitData(value);
            } else {
                if (this._isSuggestActive()) {
                    this._closeSuggester();
                }
            }
            InkEvent.stop(e);
        }

        return;
    },

    _onFocusInput: function() {
        // for now... do nothing
        return;
    },


    _getInputValue: function() {
        return this._element.value.trim();
    },

    _isSuggestActive: function() {
        return !!this.suggestActive;
    },

    _submitData: function(param) {
        if(this.ajaxRequest) {
            // close connection
            try { this.ajaxRequest.transport.abort(); } catch (e) {}
            this.ajaxRequest = null;
        }

        var input = this._getInputValue();

        if(!this._options.suggestionsObject){
            this.ajaxRequest = new Ajax(this._options.suggestionsURI, {
                method: 'get',
                parameters: 'name='+encodeURIComponent(input)+'',
                onSuccess: Ink.bindMethod(this, '_onSubmitSuccess'),
                onFailure: Ink.bindMethod(this, '_onSubmitFailure')
            });
        } else {
           this._searchSuggestions(input);
        }
    },

    _searchSuggestions: function(str) {
        if(str != '') {

            var re = new RegExp("^"+str+"", "i");

            var indexStr = 0;
            var found = false;
            var endLoop = false;

            var obj = this._options.suggestionsObject;

            var result = [];

            var totalSuggestions = obj.length;
            for(var i=0; i < totalSuggestions; i++) {
                curSuggest = obj[i];

                //if(re.test(curPath)) {
                if(curSuggest.match(re)) {
                    result.push(curSuggest);
                }
            }

            if(result.length>0) {
                this._writeResult(result);
            } else {
                this._closeSuggester();
            }
        } else {
            this._closeSuggester();
        }
    },

    _onSubmitSuccess: function(obj) {
        if(obj != null) {
            var req = obj.responseText.evalJSON();

            //Ink.ss('debug').innerHTML = '<pre>'+SAPO.Utility.Dumper.returnDump(req)+'</pre>';
            if(!req.error) {
                this._writeResult(req.suggestions);
            }
        }
    },

    _onSubmitFailure: function(err) {
        Ink.error('[Ink.UI.AutoComplete_1] Submit failure: ', err);
    },

    _clearResults: function() {
        var aUl = this.suggestPlaceElm.getElementsByTagName('ul');
        if(aUl.length > 0) {
            aUl[0].parentNode.removeChild(ul);
        }
    },

    _writeResult: function(aSuggestions) {
        this._clearResults();
        var i = 0;
        var limit = this._options.resultLimit;
        var total = aSuggestions.length;

        //var str = '';
        var ul = document.createElement('ul');

        var li = false;
        var a = false;

        if(total > 0) {
            while(i < total) {
                li = document.createElement('li');

                a = document.createElement('a');
                a.href = '#'+aSuggestions[i];
                a.title = aSuggestions[i];

                a.onclick = Ink.bind(function(value) {
                    this.setChoosedValue(value);
                    this._closeSuggester();
                    return false;
                }, this, aSuggestions[i]);

                a.onmouseover = Ink.bind(function(value) {
                    this.setMouseSelected(value);
                }, this, aSuggestions[i]);

                a.innerHTML = aSuggestions[i];
                if(i === 0) {
                    a.className = this._options.classNameSelected;
                }

                li.appendChild(a);
                ul.appendChild(li);

                /*
                str += '<input name="checkbox2" type="radio" class="formRegistocheckbox" value="checkbox" />';
                str += '<label>'+aEmails[i]+'</label><br clear="all"/>';
                i++;
                */
                i++;
                if(i == limit) {
                    break;
                }
            }

            this._openSuggester();
        }

        //this.suggestPlaceElm.innerHTML = str;
        this.suggestPlaceElm.appendChild(ul);
    },

    _closeSuggester: function() {
        this.suggestPlaceElm.style.display = 'none';
        this.suggestActive = false;
    },

    _openSuggester: function() {
        this.suggestPlaceElm.style.display = 'block';
        this.suggestActive = true;
    },

    _onSuggesterEnter: function() {
        if(this._isSuggestActive()) {
            var ul = this.suggestPlaceElm.getElementsByTagName('UL')[0] || false;
            if(ul) {
                var aLi = ul.getElementsByTagName('LI');
                var total = aLi.length;
                var i=0;
                while(i < total) {
                    if(aLi[i].childNodes[0].className == this._options.classNameSelected) {
                        aLi[i].childNodes[0].className = '';
                        var value = aLi[i].childNodes[0].innerHTML;
                        this.setChoosedValue(value);
                        break;
                    }
                    i++;
                }
            }
        }
    },

    _onClickWindow: function(e) {
        if(this._isSuggestActive()) {
            this._closeSuggester();
        }
    },

    setMouseSelected: function(value) {
        if(this._isSuggestActive()) {
            var ul = this.suggestPlaceElm.getElementsByTagName('UL')[0] || false;
            if(ul) {
                var aLi = ul.getElementsByTagName('LI');
                var total = aLi.length;
                var i = 0;
                while(i < total) {
                    if(aLi[i].childNodes[0].className == this._options.classNameSelected) {
                        aLi[i].childNodes[0].className = '';
                    }
                    if(aLi[i].childNodes[0].title == value) {
                        aLi[i].childNodes[0].className = this._options.classNameSelected;
                    }
                    i++;
                }
            }
        }
    },

    setChoosedValue: function(value) {
        //value = value.replace(/([^@]+)@(.*)/, "$1");
        this._element.value = value;
    },

    _goSuggesterDown: function() {
        if(this._isSuggestActive()) {
            var ul = this.suggestPlaceElm.getElementsByTagName('UL')[0] || false;
            if(ul) {
                var aLi = ul.getElementsByTagName('LI');
                var total = aLi.length;
                var i=0;
                var j=0;
                var selectedPosition = false;
                var nextSelected = 0;
                while(i < total) {
                    if(aLi[i].childNodes[0].className == this._options.classNameSelected) {
                        selectedPosition = i;
                        aLi[i].childNodes[0].className = '';
                        break;
                    }
                    i++;
                }
                if(selectedPosition == (total - 1)) {
                    nextSelected = 0;
                } else {
                    nextSelected = (selectedPosition + 1);
                }

                while(j < total) {
                    if(j == nextSelected) {
                        aLi[j].childNodes[0].className = this._options.classNameSelected;
                    }
                    j++;
                }
            }
        }
    },

    _goSuggesterUp: function() {
        if(this._isSuggestActive()) {
            var ul = this.suggestPlaceElm.getElementsByTagName('UL')[0] || false;
            if(ul) {
                var aLi = ul.getElementsByTagName('LI');
                var total = aLi.length;
                var i=0;
                var j=0;
                var selectedPosition = false;
                var nextSelected = 0;
                while(i < total) {
                    if(aLi[i].childNodes[0].className == this._options.classNameSelected) {
                        selectedPosition = i;
                        aLi[i].childNodes[0].className = '';
                        break;
                    }
                    i++;
                }
                if(selectedPosition == 0) {
                    nextSelected = (total - 1);
                } else {
                    nextSelected = (selectedPosition - 1);
                }

                while(j < total) {
                    if(j == nextSelected) {
                        aLi[j].childNodes[0].className = this._options.classNameSelected;
                    }
                    j++;
                }
            }
        }
    }
};

return AutoComplete;

});
