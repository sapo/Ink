var App = window.App || {};

/**
 * Attach more parameters to URL
 *
 * @param string url
 * @param object extend_with
 */
App.extendUrl = function(url, extend_with) {
  if(!url || !extend_with) {
    return url;
  } // if
  
  var extended_url = url;
  var parameters = [];
  
  extended_url += extended_url.indexOf('?') < 0 ? '?' : '&';
  
  for(var i in extend_with) {
    if(typeof(extend_with[i]) == 'object') {
      for(var j in extend_with[i]) {
        parameters.push(i + '[' + j + ']' + '=' + extend_with[i][j]);
      } // for
    } else {
      parameters.push(i + '=' + extend_with[i]);
    } // if
  } // for
  
  return extended_url + parameters.join('&');
};



$(document).ready(function() {
  App.menuButton.init();
  
  $('.listing_options form').each(function () {
    var form = $(this);
    form.submit(function () {
      var final_url = form.attr('action');
      form.find('input,select,textarea').each(function () {
        var input_element = $(this);
        if ((input_element.is(':type[checkbox]') && input_element.checked()) || !input_element.is(':type[checkbox]')) {
            final_url += final_url.indexOf('?') < 0 ? '?' : '&';
            final_url += input_element.attr('name') + '=' + input_element.val();
        } // if
      });
        
      window.location = final_url;
      return false;
    });
  })
});


/**
* Initialize menu button
*/

App.menuButton = function () {
  
  /**
   * Result
   */
  return {
    /**
    * Initialize
    */
    init: function() {
      buttonMenu = $('#button_menu');
      overlayMenu = $('#overlay_menu');
      appBody = $('#app_body');
      
      buttonMenu.click(function () {
        if (buttonMenu.is('.active')) {
          buttonMenu.removeClass('active');
          overlayMenu.hide();
          appBody.show()
        } else {
          buttonMenu.addClass('active');
          overlayMenu.show();
          appBody.hide();
        }
        return false;
      });
    } // init
    
  }
  
}();