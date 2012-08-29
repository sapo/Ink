/*
+-----------------------------------------------------------------------+
| Copyright (c) 2006-2007 Mika Tuupola, Dylan Verheul                   |
| All rights reserved.                                                  |
|                                                                       |
| Redistribution and use in source and binary forms, with or without    |
| modification, are permitted provided that the following conditions    |
| are met:                                                              |
|                                                                       |
| o Redistributions of source code must retain the above copyright      |
|   notice, this list of conditions and the following disclaimer.       |
| o Redistributions in binary form must reproduce the above copyright   |
|   notice, this list of conditions and the following disclaimer in the |
|   documentation and/or other materials provided with the distribution.|
|                                                                       |
| THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   |
| "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     |
| LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR |
| A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  |
| OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, |
| SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      |
| LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
| DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY |
| THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   |
| (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE |
| OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  |
|                                                                       |
+-----------------------------------------------------------------------+
*/

/* $Id: jquery.jeditable.js 134 2007-03-13 17:29:52Z tuupola $ */

/**
  * jQuery inplace editor plugin (version 1.2.1)  
  *
  * Based on editable by Dylan Verheul <dylan@dyve.net>
  * http://www.dyve.net/jquery/?editable
  *
  * @name  jEditable
  * @type  jQuery
  * @param String  target             POST URL or function name to send edited content
  * @param Hash    settings            additional options 
  * @param String  settings[name]      POST parameter name of edited content
  * @param String  settings[id]        POST parameter name of edited div id
  * @param String  settings[type]      text, textarea or select
  * @param Integer settings[rows]      number of rows if using textarea
  * @param Integer settings[cols]      number of columns if using textarea
  * @param String  settings[loadurl]   URL to fetch content before editing
  * @param String  settings[loadtype]  Request type for load url. Should be GET or POST.
  * @param String  settings[data]      Or content given as paramameter.
  * @param String  settings[indicator] indicator html to show when saving
  * @param String  settings[tooltip]   optional tooltip text via title attribute
  * @param String  settings[event]     jQuery event such as 'click' of 'dblclick'
  * @param String  settings[onblur]    'cancel', 'submit' or 'ignore'
  * @param String  settings[submit]    submit button value, empty means no button
  * @param String  settings[cancel]    cancel button value, empty means no button
  * @param String  settings[cssclass]  CSS class to apply to input form. 'inherit' to copy from parent.
  * @param String  settings[style]     Style to apply to input form 'inherit' to copy from parent.
  * @param String  settings[select]    true or false, when true text is highlighted
  *             
  */

jQuery.fn.editable = function(target, settings) {

    /* prevent elem has no properties error */
    if (this.length == 0) { 
        return(this); 
    };
    
    settings = jQuery.extend({
      target      : target,
      name        : 'value',
      id          : 'id',
      type        : 'text',
      event       : 'dblclick',
      onblur      : 'cancel',
      loadtype    : 'POST',
      field_class : null
    }, settings);
      
    jQuery(this).attr('title', settings.tooltip);

    jQuery(this)[settings.event](function(e) {

        /* save this to self because this changes when scope changes */
        var self = this;

        /* prevent throwing an exeption if edit field is clicked again */
        if (self.editing) {
            return;
        }

        self.editing    = true;
        self.revert     = jQuery(self).html();
        self.innerHTML  = '';

        /* create the form object */
        var f = document.createElement('form');
        
        /* apply css or style or both */
        if (settings.cssclass) {
            if ('inherit' == settings.cssclass) {
                jQuery(f).attr('class', jQuery(self).attr('class'));
            } else {
                jQuery(f).attr('class', settings.cssclass);
            }
        }
        
        if (settings.style) {
            if ('inherit' == settings.style) {
                jQuery(f).attr('style', jQuery(self).attr('style'));
            } else {
                jQuery(f).attr('style', settings.style);
            }
        }
        
        /*  main input element */
        var i;
        switch (settings.type) {
            case 'textarea':
                i = document.createElement('textarea');
                break;
            case 'select':
                i = document.createElement('select');
                break;
            default:
                i = document.createElement('input');
                i.type  = settings.type;
                /* https://bugzilla.mozilla.org/show_bug.cgi?id=236791 */
                i.setAttribute('autocomplete','off');
        }
        
        if(settings.field_class) {
          $(i).addClass(settings.field_class);
        }
        
        /* maintain bc with 1.1.1 and earlier versions */        
        if (settings.getload) {
            settings.loadurl    = settings.getload;
            settings.loadtype = 'GET';
        } else if (settings.postload) {
            settings.loadurl    = settings.postload;
            settings.loadtype = 'POST';
        }

        /* set input content via POST, GET, given data or existing value */
        if (settings.loadurl) {
            var data = {};
            data[settings.id] = self.id;
            jQuery.ajax({
               type : settings.loadtype,
               url  : settings.loadurl,
               data : data,
               success: function(str) {
                  setInputContent(str);
               }
            });
        } else if (settings.data) {
            setInputContent(settings.data);
        } else { 
            setInputContent(self.revert);
        }

        i.name  = settings.name;
        f.appendChild(i);
        
        /** Buttons div **/
        var buttons = $(document.createElement('div'));
        buttons.addClass('editableButtons');
        
        /** Submit button **/
        var submit_button = $(document.createElement('button'));
        submit_button.attr('type', 'submit');
        submit_button.html(settings.submit ? settings.submit : '<span>Submit</span>');
        
        /** Cancel button **/
        var cancel_button = $(document.createElement('button'));
        cancel_button.attr('type', 'button');
        cancel_button.html(settings.cancel ? settings.cancel : '<span>Cancel</span>');
        cancel_button.click(function() {
          reset();
        });
        
        /** And build **/
        buttons.append(submit_button);
        buttons.append(' ');
        buttons.append(cancel_button);
        
        $(f).append(buttons);

        /* add created form to self */
        self.appendChild(f);

        i.focus();
        
        /* highlight input contents when requested */
        if (settings.select) {
            i.select();
        }
         
        /* discard changes if pressing esc */
        jQuery(i).keydown(function(e) {
            if (e.keyCode == 27) {
                e.preventDefault();
                reset();
            }
        });

        /* discard, submit or nothing with changes when clicking outside */
        /* do nothing is usable when navigating with tab */
        var t;
        if ('cancel' == settings.onblur) {
            jQuery(i).blur(function(e) {
                t = setTimeout(reset, 500)
            });
        /* TODO: does not currently work */
        } else if ('submit' == settings.onblur) {
            jQuery(i).blur(function(e) {
                jQuery(f).submit();
            });
        } else {
            jQuery(i).blur(function(e) {
              /* TODO: maybe something here */
            });
        }

        jQuery(f).submit(function(e) {

            if (t) { 
                clearTimeout(t);
            }

            /* do no submit */
            e.preventDefault(); 

            /* check if given target is function */
            if (Function == settings.target.constructor) {
                var str = settings.target.apply(self, [jQuery(i).val()]);
                self.innerHTML = str;
                self.editing = false;
            } else {
                /* add edited content and id of edited element to POST */           
                var p = {'submitted' : 'submitted'};
                p[i.name] = jQuery(i).val();
                p[settings.id] = self.id;

                /* show the saving indicator */
                jQuery(self).html(settings.indicator);
                jQuery.post(settings.target, p, function(str) {
                    self.innerHTML = str;
                    self.editing = false;
                });
            }
            return false;
        });

        function reset() {
            self.innerHTML = self.revert;
            self.editing   = false;
        };
        
        function setInputContent(str) {
            switch (settings.type) { 	 
                case 'select': 	 
                    if (String == str.constructor) { 	 
                        eval ("var json = " + str);
                        for (var key in json) {
                            if ('selected' == key) {
                                continue;
                            } 
                            o = document.createElement('option'); 	 
                            o.value = key;
                            var text = document.createTextNode(json[key]);
                            o.appendChild(text)
                            if (key == json['selected']) {
                                o.selected = true;
                            }
                            i.appendChild(o); 	 
                        }
                    } 	 
                    break; 	 
                default: 	 
                    i.value = str; 	 
                    break; 	 
            } 	 
        }

    });

    return(this);
}
