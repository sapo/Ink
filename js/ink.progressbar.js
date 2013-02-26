(function(undefined){

    'use strict';

    /**
     * Check if dependencies are loaded
     */
    var
        dependencies = ['SAPO','SAPO.Dom.Selector','SAPO.Dom.Event','SAPO.Dom.Css'],
        i, j,
        dependencyTree,
        dependency
    ;
    for(i = 0; i<dependencies.length;i+=1){
        dependencyTree = dependencies[i].split('.');
        dependency = window;
        for( j=0; j<dependencyTree.length; j+=1 ){
            if( !(dependencyTree[j] in dependency) ){
                throw '[SAPO.Ink.ProgressBar] :: Dependency not met ( ' + dependencyTree.join('.') + ' )';
            }
            dependency = dependency[dependencyTree[j]];
        }
    }


    SAPO.namespace('Ink');

    /**
     * The component
     */
    var ProgressBar = function( selector, options ){

        if( typeof selector !== 'object' ){
            if( typeof selector !== 'string' ){
                throw '[SAPO.Ink.ProgressBar] :: Invalid selector';
            } else {
                this._element = SAPO.Dom.Selector.select(selector);
                if( this._element.length < 1 ){
                    throw "[SAPO.Ink.ProgressBar] :: Selector didn't find any elements";
                }
                this._element = this._element[0];
            }
        } else {
            this._element = selector;
        }


        this._options = SAPO.extendObj({
            'startValue': 0,
            'onStart': function(){},
            'onEnd': function(){}
        },SAPO.Dom.Element.data(this._element));

        this._options = SAPO.extendObj( this._options, options || {});

        this._init();
    };

    ProgressBar.prototype = {
        _init: function(){
            this._elementBar = SAPO.Dom.Selector.select('.bar',this._element);
            if( this._elementBar.length < 1 ){
                throw '[SAPO.Ink.ProgressBar] :: Bar element not found';
            }
            this._elementBar = this._elementBar[0];

            this._options.onStart = this._options.onStart.bindObj(this);
            this._options.onEnd = this._options.onEnd.bindObj(this);
            this.setValue( this._options.startValue );
        },

        setValue: function( newValue ){
            this._options.onStart();

            newValue = parseInt(newValue,10);
            if( isNaN(newValue) || (newValue < 0) ){
                newValue = 0;
            } else if( newValue>100 ){
                newValue = 100;
            }
            this._elementBar.style.width =  newValue + '%';

            this._options.onEnd();
        }
    };

    SAPO.Ink.ProgressBar = ProgressBar;

})();


/**
 * This file has the ProgressBar component
 */