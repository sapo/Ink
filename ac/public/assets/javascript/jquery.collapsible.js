/**
* Released under the terms or MIT license:
*
* Copyright (c) 2007 Ilija Studen
* 
* Permission is hereby granted, free of charge, to any person obtaining a copy 
* of this software and associated documentation files (the "Software"), to deal 
* in the Software without restriction, including without limitation the rights 
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
* copies of the Software, and to permit persons to whom the Software is 
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all 
* copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE 
* SOFTWARE.
*/

jQuery.fn.collapsible = function(settings) {
  settings = jQuery.extend({
    collapsed          : false,
    slide_speed        : null,
    on_before_collapse : null,
    on_collapse        : null,
    on_before_expand   : null,
    on_expand          : null
  }, settings);
  
  return this.each(function() {
    var fieldset = jQuery(this);
    
    var legend = fieldset.find(':first');
    var body = jQuery(document.createElement('div'));
    
    var children = fieldset.children();
    var len = children.length;
    if(len > 1) {
      for(i = 1; i < len; i++) {
        jQuery(children[1]).appendTo(body);
      } // for
    } // if
    
    fieldset.addClass('collapsibleFieldset');
    body.addClass('collapsibleFieldsetBody');
    legend.addClass('collapsibleFieldsetTitle').after(body);
    
    if(settings.collapsed) {
      fieldset.addClass('collapsed');
      body.hide();
    } else {
      fieldset.removeClass('collapsed');
    } // if
    
    legend.click(function(e) {
      
      // Visible? Collapse...
      if(body.css('display') == 'block') {
        if(settings.on_before_collapse) {
          if(!settings.on_before_collapse(fieldset)) {
            return;
          } // if
        } // if
        
        body.slideUp(settings.slide_speed, function() {
          fieldset.addClass('collapsed');
          if(settings.on_collapse) {
            settings.on_collapse(fieldset);
          } // if
        });
        
      // Collapsed? Show...
      } else {
        if(settings.on_before_expand) {
          if(!settings.on_before_expand(fieldset)) {
            return;
          } // if
        } // if
        
        body.slideDown(settings.slide_speed, function() {
          fieldset.removeClass('collapsed');
          if(settings.on_expand) {
            settings.on_expand(fieldset);
          } // if
        });
      } // if
      
    });
  });
}